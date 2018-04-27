<?php

namespace Remorhaz\UniLex\Lexer;

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
use Remorhaz\UniLex\Unicode\CharBufferFactory;
use Throwable;

class TokenMatcherGenerator
{

    private $spec;

    private $output;

    private $dfa;

    /**
     * @var TokenSpec[]
     */
    private $tokenNfaStateMap = [];

    public function __construct(TokenMatcherSpec $spec)
    {
        $this->spec = $spec;
    }

    /**
     * @return string
     * @throws Exception
     * @throws \ReflectionException
     */
    private function buildOutput(): string
    {
        return
            "<?php\n{$this->buildFileComment()}\n" .
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
        return new $targetClass;
    }

    /**
     * @return string
     * @throws Exception
     * @throws \ReflectionException
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
            $comment .= rtrim(" * {$commentLine}") ."\n";
        }
        $comment .= " */\n";
        return $comment;
    }

    /**
     * @return string
     * @throws \ReflectionException
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
     * @throws \ReflectionException
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
     * @throws \ReflectionException
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

        foreach ($this->spec->getContextList() as $context) {
            $result .=
                $this->buildFsmEntry($context) .
                $this->buildFsmMoves($context);
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
     * @param string $context
     * @return string
     * @throws Exception
     */
    private function buildFsmEntry(string $context): string
    {
        return $this->buildMethodPart("goto state{$this->getDfa($context)->getStateMap()->getStartState()};\n");
    }

    /**
     * @param string $context
     * @return string
     * @throws Exception
     */
    private function buildFsmMoves(string $context): string
    {
        $result = '';
        foreach ($this->getDfa($context)->getStateMap()->getStateList() as $stateIn) {
            if ($this->isFinishStateWithSingleEnteringTransition($context, $stateIn)) {
                continue;
            }
            $result .=
                $this->buildStateEntry($context, $stateIn) .
                $this->buildStateTransitionList($context, $stateIn) .
                $this->buildStateFinish($context, $stateIn);
        }

        return $result;
    }

    /**
     * @param string $context
     * @param int $stateIn
     * @return string
     * @throws Exception
     */
    private function buildStateEntry(string $context, int $stateIn): string
    {
        $result = '';
        $result .= $this->buildMethodPart("state{$stateIn}:");
        $moves = $this->getDfa($context)->getTransitionMap()->getExitList($stateIn);
        if (empty($moves)) {
            return $result;
        }
        $result .= $this->buildMethodPart("if (\$context->getBuffer()->isEnd()) {");
        $result .= $this->getDfa($context)->getStateMap()->isFinishState($stateIn)
            ? $this->buildMethodPart("goto finish{$stateIn};", 3)
            : $this->buildMethodPart("goto error;", 3);
        $result .=
            $this->buildMethodPart("}") .
            $this->buildMethodPart("\$char = \$context->getBuffer()->getSymbol();");
        return $result;
    }

    /**
     * @param string $context
     * @param int $stateIn
     * @return string
     * @throws Exception
     */
    private function buildStateTransitionList(string $context, int $stateIn): string
    {
        $result = '';
        foreach ($this->getDfa($context)->getTransitionMap()->getExitList($stateIn) as $stateOut => $symbolList) {
            foreach ($symbolList as $symbol) {
                $rangeSet = $this->getDfa($context)->getSymbolTable()->getRangeSet($symbol);
                $result .=
                    $this->buildMethodPart("if ({$this->buildRangeSetCondition($rangeSet)}) {") .
                    $this->buildOnTransition() .
                    $this->buildMethodPart("\$context->getBuffer()->nextSymbol();", 3);
                $result .= $this->isFinishStateWithSingleEnteringTransition($context, $stateOut)
                    ? $this->buildToken($context, $stateOut, 3)
                    : $this->buildMethodPart("goto state{$stateOut};", 3);
                $result .= $this->buildMethodPart("}");
            }
        }
        return $result;
    }

    /**
     * @param string $context
     * @param int $stateOut
     * @return bool
     * @throws Exception
     */
    private function isFinishStateWithSingleEnteringTransition(string $context, int $stateOut): bool
    {
        if (!$this->getDfa($context)->getStateMap()->isFinishState($stateOut)) {
            return false;
        }
        $enters = $this->getDfa($context)->getTransitionMap()->getEnterList($stateOut);
        $exits = $this->getDfa($context)->getTransitionMap()->getExitList($stateOut);
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
            return $result;
        }
        $result = $this->buildMethodPart(implode(" ||\n", $conditionList), 1);
        return ltrim($result);
    }

    /**
     * @param string $context
     * @param int $stateIn
     * @return string
     * @throws Exception
     */
    private function buildStateFinish(string $context, int $stateIn): string
    {
        if (!$this->getDfa($context)->getStateMap()->isFinishState($stateIn)) {
            return $this->buildMethodPart("goto error;\n");
        }
        $result = '';
        if (!empty($this->getDfa($context)->getTransitionMap()->getExitList($stateIn))) {
            $result .= $this->buildMethodPart("finish{$stateIn}:");
        }
        $result .= "{$this->buildToken($context, $stateIn)}\n";
        return $result;
    }

    /**
     * @param string $context
     * @param int $stateIn
     * @param int $indent
     * @return string
     * @throws Exception
     */
    private function buildToken(string $context, int $stateIn, int $indent = 2): string
    {
        $tokenSpec = $this->getTokenSpec($context, $stateIn);
        return
            $this->buildMethodPart($tokenSpec->getCode(), $indent) .
            $this->buildOnToken($indent) .
            $this->buildMethodPart("return true;", $indent);
    }

    /**
     * @param string $context
     * @param int $stateIn
     * @return TokenSpec
     * @throws Exception
     */
    private function getTokenSpec(string $context, int $stateIn): TokenSpec
    {
        $tokenSpec = null;
        foreach ($this->getDfa($context)->getStateMap()->getStateValue($stateIn) as $nfaFinishState) {
            if (isset($this->tokenNfaStateMap[$nfaFinishState])) {
                $tokenSpec = $this->tokenNfaStateMap[$nfaFinishState];
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
        $nfa = new Nfa;
        $startState = $nfa->getStateMap()->createState();
        $nfa->getStateMap()->setStartState($startState);
        $oldFinishStates = [];
        $this->tokenNfaStateMap = [];
        foreach ($this->spec->getTokenSpecList($context) as $tokenSpec) {
            $regExpEntryState = $nfa->getStateMap()->createState();
            $nfa
                ->getEpsilonTransitionMap()
                ->addTransition($startState, $regExpEntryState, true);
            $this->buildRegExp($nfa, $regExpEntryState, $tokenSpec->getRegExp());
            $finishStates = $nfa->getStateMap()->getFinishStateList();
            $newFinishStates = array_diff($finishStates, $oldFinishStates);
            foreach ($newFinishStates as $finishState) {
                $this->tokenNfaStateMap[$finishState] = $tokenSpec;
            }
            $oldFinishStates = $finishStates;
        }
        return DfaBuilder::fromNfa($nfa);
    }

    /**
     * @param Nfa $nfa
     * @param int $entryState
     * @param string $regExp
     * @throws Exception
     */
    private function buildRegExp(Nfa $nfa, int $entryState, string $regExp): void
    {
        $buffer = CharBufferFactory::createFromString($regExp);
        $tree = new Tree;
        ParserFactory::createFromBuffer($tree, $buffer)->run();
        $nfaBuilder = new NfaBuilder($nfa);
        $nfaBuilder->setStartState($entryState);
        (new Translator($tree, $nfaBuilder))->run();
    }
}
