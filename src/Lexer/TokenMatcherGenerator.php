<?php

namespace Remorhaz\UniLex\Lexer;

use ReflectionException;
use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\RegExp\FSM\Dfa;
use Remorhaz\UniLex\RegExp\FSM\DfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\Nfa;
use Remorhaz\UniLex\RegExp\FSM\NfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;
use Remorhaz\UniLex\RegExp\ParserFactory;
use Remorhaz\UniLex\RegExp\PropertyLoader;
use Remorhaz\UniLex\Unicode\CharBufferFactory;
use Throwable;

use function array_diff;
use function array_fill_keys;
use function array_intersect;
use function array_keys;
use function implode;
use function in_array;

class TokenMatcherGenerator
{

    private $spec;

    private $output;

    private $dfa;

    /**
     * @var TokenSpec[]
     */
    private $tokenNfaStateMap = [];

    private $regExpMap = [];

    public function __construct(TokenMatcherSpec $spec)
    {
        $this->spec = $spec;
    }

    /**
     * @return string
     * @throws Exception
     * @throws ReflectionException
     */
    private function buildOutput(): string
    {
        return
            "<?php\n\n{$this->buildFileComment()}\n" .
            "{$this->buildHeader()}\n" .
            "class {$this->spec->getTargetShortName()} extends {$this->spec->getTemplateClass()->getShortName()}\n" .
            "{\n" .
            "\n" .
            "    public function match({$this->buildMatchParameters()}): bool\n" .
            "    {\n{$this->buildMatchBody()}" .
            "    }\n" .
            "}\n";
    }

    /**
     * @return TokenMatcherInterface
     * @throws Exception
     */
    public function load(): TokenMatcherInterface
    {
        $targetClass = $this->spec->getTargetClassName();
        if (!class_exists($targetClass)) {
            try {
                $source = $this->getOutput();
                eval("?>{$source}");
            } catch (Throwable $e) {
                throw new Exception("Invalid PHP code generated", 0, $e);
            }
            if (!class_exists($targetClass)) {
                throw new Exception("Failed to generate target class");
            }
        }

        return new $targetClass();
    }

    /**
     * @return string
     * @throws Exception
     * @throws ReflectionException
     */
    public function getOutput(): string
    {
        if (!isset($this->output)) {
            $this->output = $this->buildOutput();
        }

        return $this->output;
    }

    private function buildFileComment(): string
    {
        $content = $this->spec->getFileComment();
        if ('' == $content) {
            return '';
        }
        $comment = "/**\n";
        $commentLineList = explode("\n", $content);
        foreach ($commentLineList as $commentLine) {
            $comment .= rtrim(" * {$commentLine}") . "\n";
        }
        $comment .= " */\n";

        return $comment;
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public function buildHeader(): string
    {
        $headerParts = [];
        $namespace = $this->spec->getTargetNamespaceName();
        if ($namespace != '') {
            $headerParts[] = $this->buildMethodPart("namespace {$namespace};", 0);
        }
        $useList = $this->buildUseList();
        if ('' != $useList) {
            $headerParts[] = $useList;
        }
        $header = $this->buildMethodPart($this->spec->getHeader(), 0);
        if ('' != $header) {
            $headerParts[] = $header;
        }

        return implode("\n", $headerParts);
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    private function buildUseList(): string
    {
        $result = '';
        foreach ($this->spec->getUsedClassList() as $alias => $className) {
            $classWithAlias = is_string($alias) ? "{$className} {$alias}" : $className;
            $result .= $this->buildMethodPart("use {$classWithAlias};", 0);
        }

        return $result;
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    private function buildMatchParameters(): string
    {
        $paramList = [];
        foreach ($this->spec->getMatchMethod()->getParameters() as $matchParameter) {
            if ($matchParameter->hasType()) {
                $param = $matchParameter->getType()->isBuiltin()
                    ? $matchParameter->getType()->getName()
                    : $matchParameter->getClass()->getShortName();
                $param .= " ";
            } else {
                $param = "";
            }
            $param .= "\${$matchParameter->getName()}";
            $paramList[] = $param;
        }

        return implode(", ", $paramList);
    }

    /**
     * @return string
     * @throws Exception
     */
    private function buildMatchBody(): string
    {
        $result = $this->buildBeforeMatch();

        foreach ($this->spec->getModeList() as $mode) {
            if (TokenMatcherInterface::DEFAULT_MODE == $mode) {
                continue;
            }
            $result .=
                $this->buildMethodPart("if (\$context->getMode() == '{$mode}') {") .
                $this->buildFsmEntry($mode, 3) .
                $this->buildMethodPart("}");
        }
        foreach ($this->spec->getModeList() as $mode) {
            if (TokenMatcherInterface::DEFAULT_MODE == $mode) {
                $result .= $this->buildFsmEntry(TokenMatcherInterface::DEFAULT_MODE) . "\n";
            }
            $result .= $this->buildFsmMoves($mode);
        }

        $result .= $this->buildErrorState();

        return $result;
    }

    private function buildBeforeMatch(): string
    {
        $code = $this->spec->getBeforeMatch();

        return
            $this->buildMethodPart("\$context = \$this->createContext(\$buffer, \$tokenFactory);") .
            $this->buildMethodPart($code);
    }

    /**
     * @param string $mode
     * @param int    $indent
     * @return string
     * @throws Exception
     */
    private function buildFsmEntry(string $mode, int $indent = 2): string
    {
        $state = $this->getDfa($mode)->getStateMap()->getStartState();

        return $this
            ->buildMethodPart("goto {$this->buildStateLabel('state', $mode, $state)};", $indent);
    }

    private function buildStateLabel(string $prefix, string $mode, int $state): string
    {
        $contextSuffix = TokenMatcherInterface::DEFAULT_MODE == $mode
            ? ''
            : ucfirst($mode);

        return "{$prefix}{$contextSuffix}{$state}";
    }

    /**
     * @param string $mode
     * @return string
     * @throws Exception
     */
    private function buildFsmMoves(string $mode): string
    {
        $result = '';
        foreach ($this->getDfa($mode)->getStateMap()->getStateList() as $stateIn) {
            if ($this->isFinishStateWithSingleEnteringTransition($mode, $stateIn)) {
                continue;
            }
            $result .=
                $this->buildStateEntry($mode, $stateIn) .
                $this->buildStateTransitionList($mode, $stateIn) .
                $this->buildStateFinish($mode, $stateIn);
        }

        return $result;
    }

    /**
     * @param string $mode
     * @param int    $stateIn
     * @return string
     * @throws Exception
     */
    private function buildStateEntry(string $mode, int $stateIn): string
    {
        $result = '';
        $result .= $this->buildMethodPart("{$this->buildStateLabel('state', $mode, $stateIn)}:");
        $result .= $this->buildMethodPart("\$context->visitState({$stateIn});");
        $moves = $this->getDfa($mode)->getTransitionMap()->getExitList($stateIn);
        if (empty($moves)) {
            return $result;
        }
        $result .= $this->buildMethodPart("if (\$context->getBuffer()->isEnd()) {");
        $result .= $this->getDfa($mode)->getStateMap()->isFinishState($stateIn)
            ? $this->buildMethodPart("goto {$this->buildStateLabel('finish', $mode, $stateIn)};", 3)
            : $this->buildMethodPart("goto error;", 3);
        $result .=
            $this->buildMethodPart("}") .
            $this->buildMethodPart("\$char = \$context->getBuffer()->getSymbol();");

        return $result;
    }

    /**
     * @param string $mode
     * @param int    $stateIn
     * @return string
     * @throws Exception
     */
    private function buildStateTransitionList(string $mode, int $stateIn): string
    {
        $result = '';
        foreach ($this->getDfa($mode)->getTransitionMap()->getExitList($stateIn) as $stateOut => $symbolList) {
            foreach ($symbolList as $symbol) {
                $rangeSet = $this->getDfa($mode)->getSymbolTable()->getRangeSet($symbol);
                $result .=
                    $this->buildMethodPart("if ({$this->buildRangeSetCondition($rangeSet)}) {") .
                    $this->buildOnTransition() .
                    $this->buildMethodPart("\$context->getBuffer()->nextSymbol();", 3);
                $result .= $this->isFinishStateWithSingleEnteringTransition($mode, $stateOut)
                    ? $this->buildToken($mode, $stateOut, 3)
                    : $this->buildMethodPart("goto {$this->buildStateLabel('state', $mode, $stateOut)};", 3);
                $result .= $this->buildMethodPart("}");
            }
        }

        return $result;
    }

    /**
     * @param string $mode
     * @param int    $stateOut
     * @return bool
     * @throws Exception
     */
    private function isFinishStateWithSingleEnteringTransition(string $mode, int $stateOut): bool
    {
        if (!$this->getDfa($mode)->getStateMap()->isFinishState($stateOut)) {
            return false;
        }
        $enters = $this->getDfa($mode)->getTransitionMap()->getEnterList($stateOut);
        $exits = $this->getDfa($mode)->getTransitionMap()->getExitList($stateOut);
        if (!(count($enters) == 1 && count($exits) == 0)) {
            return false;
        }
        $symbolList = array_pop($enters);

        return count($symbolList) == 1;
    }

    private function buildHex(int $char): string
    {
        $hexChar = strtoupper(dechex($char));
        if (strlen($hexChar) % 2 != 0) {
            $hexChar = "0{$hexChar}";
        }

        return "0x{$hexChar}";
    }

    private function buildRangeCondition(Range $range): array
    {
        $startChar = $this->buildHex($range->getStart());
        if ($range->getStart() == $range->getFinish()) {
            return ["{$startChar} == \$char"];
        }
        $finishChar = $this->buildHex($range->getFinish());
        if ($range->getStart() + 1 == $range->getFinish()) {
            return [
                "{$startChar} == \$char",
                "{$finishChar} == \$char",
            ];
        }

        return ["{$startChar} <= \$char && \$char <= {$finishChar}"];
    }

    private function buildRangeSetCondition(RangeSet $rangeSet): string
    {
        $conditionList = [];
        foreach ($rangeSet->getRanges() as $range) {
            $conditionList = array_merge($conditionList, $this->buildRangeCondition($range));
        }
        $result = implode(" || ", $conditionList);
        if (strlen($result) + 15 <= 120 || count($conditionList) == 1) {
            return ltrim($result);
        }
        $result = $this->buildMethodPart(implode(" ||\n", $conditionList), 1);

        return "\n    " . ltrim($result);
    }

    /**
     * @param string $mode
     * @param int    $stateIn
     * @return string
     * @throws Exception
     */
    private function buildStateFinish(string $mode, int $stateIn): string
    {
        if (!$this->getDfa($mode)->getStateMap()->isFinishState($stateIn)) {
            return $this->buildMethodPart("goto error;\n");
        }
        $result = '';
        if (!empty($this->getDfa($mode)->getTransitionMap()->getExitList($stateIn))) {
            $result .= $this->buildMethodPart("{$this->buildStateLabel('finish', $mode, $stateIn)}:");
        }
        $result .= "{$this->buildToken($mode, $stateIn)}\n";

        return $result;
    }

    /**
     * @param string $mode
     * @param int    $stateIn
     * @param int    $indent
     * @return string
     * @throws Exception
     */
    private function buildToken(string $mode, int $stateIn, int $indent = 2): string
    {
        $result = '';
        foreach ($this->regExpMap as $regExp => [$allowedStateIds, $forbiddenStateIds]) {
            if (!in_array($stateIn, $allowedStateIds)) {
                continue;
            }
            $allowedStates = implode(', ', $allowedStateIds);
            $forbiddenStates = implode(', ', $forbiddenStateIds);
            $condition = "\$context->checkVisitedStates([{$allowedStates}], [{$forbiddenStates}])";
            $tokenSpec = $this->spec->getTokenSpec($mode, $regExp);
            $result .=
                $this->buildMethodPart("if ({$condition}) {", $indent) .
                $this->buildSingleToken($tokenSpec, $indent + 1) .
                $this->buildMethodPart("}", $indent);
        }

        if ('' === $result) {
            throw new Exception("No tokens found for state {$stateIn}");
        }

        return $result;
    }

    private function buildSingleToken(TokenSpec $tokenSpec, int $indent): string
    {
        return
            $this->buildMethodPart("// {$tokenSpec->getRegExp()}", $indent) .
            $this->buildMethodPart($tokenSpec->getCode(), $indent) .
            $this->buildOnToken($indent) . "\n" .
            $this->buildMethodPart("return true;", $indent);
    }

    /**
     * @param string $context
     * @param int    $stateIn
     * @return TokenSpec
     * @throws Exception
     */
    private function getTokenSpec(string $context, int $stateIn): TokenSpec
    {
        $tokenSpec = null;
        foreach ($this->getDfa($context)->getStateMap()->getStateValue($stateIn) as $nfaFinishState) {
            if (isset($this->tokenNfaStateMap[$context][$nfaFinishState])) {
                $tokenSpec = $this->tokenNfaStateMap[$context][$nfaFinishState];
                break;
            }
        }
        if (!isset($tokenSpec)) {
            throw new Exception("Token spec is not defined for state {$stateIn}");
        }

        return $tokenSpec;
    }

    private function buildErrorState(): string
    {
        $code = $this->spec->getOnError();

        return
            $this->buildMethodPart("error:") .
            $this->buildMethodPart('' == $code ? "return false;" : $code);
    }

    private function buildMethodPart(string $code, int $indent = 2): string
    {
        if ('' == $code) {
            return '';
        }
        $result = '';
        $codeLineList = explode("\n", $code);
        foreach ($codeLineList as $codeLine) {
            $line = '';
            for ($i = 0; $i < $indent; $i++) {
                $line .= "    ";
            }
            $result .= rtrim($line . $codeLine) . "\n";
        }

        return $result;
    }

    private function buildOnTransition(): string
    {
        return $this->buildMethodPart($this->spec->getOnTransition(), 3);
    }

    private function buildOnToken(int $indent = 2): string
    {
        return $this->buildMethodPart($this->spec->getOnToken(), $indent);
    }

    /**
     * @param string $context
     * @return Dfa
     * @throws Exception
     */
    private function getDfa(string $context): Dfa
    {
        if (!isset($this->dfa[$context])) {
            $this->dfa[$context] = $this->buildDfa($context);
        }

        return $this->dfa[$context];
    }

    /**
     * @param string $context
     * @return Dfa
     * @throws Exception
     */
    private function buildDfa(string $context): Dfa
    {
        $nfa = new Nfa();
        $startState = $nfa->getStateMap()->createState();
        $nfa->getStateMap()->addStartState($startState);
        $oldFinishStates = [];
        $nfaRegExpMap = [];
        $this->tokenNfaStateMap[$context] = [];
        foreach ($this->spec->getTokenSpecList($context) as $tokenSpec) {
            $existingStates = $nfa->getStateMap()->getStateList();
            $regExpEntryState = $nfa->getStateMap()->createState();
            $nfa
                ->getEpsilonTransitionMap()
                ->addTransition($startState, $regExpEntryState, true);
            $this->buildRegExp($nfa, $regExpEntryState, $tokenSpec->getRegExp());
            $finishStates = $nfa->getStateMap()->getFinishStateList();
            $nfaRegExpMap[$tokenSpec->getRegExp()] = array_diff(
                $nfa->getStateMap()->getStateList(),
                $existingStates
            );
            $newFinishStates = array_diff($finishStates, $oldFinishStates);
            foreach ($newFinishStates as $finishState) {
                $this->tokenNfaStateMap[$context][$finishState] = $tokenSpec;
            }
            $oldFinishStates = $finishStates;
        }

        $dfa = DfaBuilder::fromNfa($nfa);
        $dfaRegExpMap = array_fill_keys(array_keys($nfaRegExpMap), []);
        $allDfaStateIds = $dfa->getStateMap()->getStateList();
        foreach ($allDfaStateIds as $dfaStateId) {
            $nfaStateIds = $dfa->getStateMap()->getStateValue($dfaStateId);
            foreach ($nfaRegExpMap as $regExp => $nfaRegExpStateIds) {
                if (!empty(array_intersect($nfaStateIds, $nfaRegExpStateIds))) {
                    $dfaRegExpMap[$regExp][] = $dfaStateId;
                }
            }
        }
        foreach ($dfaRegExpMap as $regExp => $regExpStateIds) {
            $this->regExpMap[$regExp] = [$regExpStateIds, array_diff($allDfaStateIds, $regExpStateIds)];
        }

        return $dfa;
    }

    /**
     * @param Nfa    $nfa
     * @param int    $entryState
     * @param string $regExp
     * @throws Exception
     */
    private function buildRegExp(Nfa $nfa, int $entryState, string $regExp): void
    {
        $buffer = CharBufferFactory::createFromString($regExp);
        $tree = new Tree();
        ParserFactory::createFromBuffer($tree, $buffer)->run();
        $nfaBuilder = new NfaBuilder($nfa, PropertyLoader::create());
        $nfaBuilder->setStartState($entryState);
        (new Translator($tree, $nfaBuilder))->run();
    }
}
