<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

use ReflectionClass;
use ReflectionException;
use Remorhaz\IntRangeSets\RangeInterface;
use Remorhaz\UCD\PropertyRangeLoader;
use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\RegExp\FSM\Dfa;
use Remorhaz\UniLex\RegExp\FSM\DfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\LanguageBuilder;
use Remorhaz\UniLex\RegExp\FSM\Nfa;
use Remorhaz\UniLex\RegExp\FSM\NfaBuilder;
use Remorhaz\UniLex\RegExp\ParserFactory;
use Remorhaz\UniLex\Unicode\CharBufferFactory;
use Throwable;

use function array_diff;
use function array_intersect;
use function array_merge;
use function array_pop;
use function array_unique;
use function count;
use function implode;
use function in_array;
use function str_repeat;

class TokenMatcherGenerator
{
    private $output;

    private $dfa;

    private $regExpFinishMap = [];

    private $conditionFunctions = [];

    public function __construct(
        private TokenMatcherSpec $spec,
    ) {
    }

    /**
     * @return string
     * @throws Exception
     * @throws ReflectionException
     */
    private function buildOutput(): string
    {
        $this->conditionFunctions = [];

        return
            "{$this->buildFileComment()}\ndeclare(strict_types=1);\n\n" .
            "{$this->buildHeader()}\n" .
            "class {$this->spec->getTargetShortName()} extends {$this->spec->getTemplateClass()->getShortName()}\n" .
            "{\n" .
            "    public function match({$this->buildMatchParameters()}): bool\n" .
            "    {\n{$this->buildMatchBody()}" .
            "    }\n" .
            $this->buildConditionFunctions() .
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
                $source = $this->getOutput(false);
                eval($source);
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
     * @throws Exception
     * @throws ReflectionException
     */
    public function getOutput(bool $asFile = true): string
    {
        $this->output ??= $this->buildOutput();

        return $asFile ? "<?php\n\n$this->output" : $this->output;
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
     * @throws ReflectionException
     */
    private function buildMatchParameters(): string
    {
        $paramList = [];
        foreach ($this->spec->getMatchMethod()->getParameters() as $matchParameter) {
            if ($matchParameter->hasType()) {
                $param = $matchParameter->getType()->isBuiltin()
                    ? $matchParameter->getType()->getName()
                    : (new ReflectionClass($matchParameter->getType()->getName()))->getShortName();
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
        return
            $this->buildMethodPart("\$context = \$this->createContext(\$buffer, \$tokenFactory);") .
            $this->buildMethodPart($this->spec->getBeforeMatch());
    }

    /**
     * @return string
     * @throws Exception
     */
    private function buildFsmEntry(string $mode, int $indent = 2): string
    {
        $state = $this->getDfa($mode)->getStateMap()->getStartState();

        return $this->buildMethodPart("goto {$this->buildStateLabel('state', $mode, $state)};", $indent);
    }

    private function buildStateLabel(string $prefix, string $mode, int $state): string
    {
        $contextSuffix = TokenMatcherInterface::DEFAULT_MODE == $mode
            ? ''
            : ucfirst($mode);

        return "$prefix$contextSuffix$state";
    }

    /**
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
     * @throws Exception
     */
    private function buildStateEntry(string $mode, int $stateIn): string
    {
        $result = '';
        $result .= $this->buildMethodPart("{$this->buildStateLabel('state', $mode, $stateIn)}:");
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
     * @throws Exception
     */
    private function buildStateTransitionList(string $mode, int $stateIn): string
    {
        $result = '';
        foreach ($this->getDfa($mode)->getTransitionMap()->getExitList($stateIn) as $stateOut => $symbolList) {
            foreach ($symbolList as $symbol) {
                $result .=
                    $this->buildMethodPart("if ({$this->buildRangeSetCondition($mode, $symbol)}) {") .
                    $this->buildOnTransition() .
                    $this->buildMethodPart("\$context->getBuffer()->nextSymbol();", 3);
                $result .= $this->isFinishStateWithSingleEnteringTransition($mode, $stateOut)
                    ? $this->buildToken($mode, $stateOut, 3)
                    : $this->buildStateTransition($mode, $stateOut, 3);
                $result .= $this->buildMethodPart("}");
            }
        }

        return $result;
    }

    private function buildStateTransition(string $mode, int $stateOut, int $indent = 3): string
    {
        return $this->buildMethodPart("goto {$this->buildStateLabel('state', $mode, $stateOut)};", $indent);
    }

    /**
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
            $hexChar = "0$hexChar";
        }

        return "0x$hexChar";
    }

    private function buildRangeCondition(RangeInterface $range): array
    {
        $startChar = $this->buildHex($range->getStart());
        if ($range->getStart() == $range->getFinish()) {
            return ["$startChar == \$char"];
        }
        $finishChar = $this->buildHex($range->getFinish());
        if ($range->getStart() + 1 == $range->getFinish()) {
            return [
                "$startChar == \$char",
                "$finishChar == \$char",
            ];
        }

        return ["$startChar <= \$char && \$char <= $finishChar"];
    }

    /**
     * @throws Exception
     */
    private function buildRangeSetCondition(string $mode, int $symbol): string
    {
        $rangeSet = $this->getDfa($mode)->getSymbolTable()->getRangeSet($symbol);

        $conditionList = [];
        foreach ($rangeSet->getRanges() as $range) {
            $conditionList = array_merge($conditionList, $this->buildRangeCondition($range));
        }
        $result = implode(" || ", $conditionList);
        if (strlen($result) + 15 <= 120 || count($conditionList) == 1) {
            return ltrim($result);
        }
        $result = $this->buildMethodPart(implode(" ||\n", $conditionList), 1);
        if (count($conditionList) > 10) {
            $method = "isMode" . ucfirst($mode) . "Symbol{$symbol}";
            $this->conditionFunctions[$method] = $result;

            return "\$this->$method(\$char)";
        }

        return "\n    " . ltrim($result);
    }

    private function buildConditionFunctions(): string
    {
        $result = '';

        foreach ($this->conditionFunctions as $method => $conditionList) {
            $result .=
                "\n    private function {$method}(int \$char): bool\n    {\n" .
                $this->buildMethodPart("return") .
                $this->buildMethodPart(rtrim($conditionList) . ';') .
                "    }\n";
        }

        return $result;
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
        $tokenSpec = $this->spec->getTokenSpec(
            $mode,
            $this->regExpFinishMap[$mode][$stateIn]
                ?? throw new Exception("No regular expressions found for state $mode:$stateIn"),
        );

        return
            $this->buildMethodPart("// {$tokenSpec->getRegExp()}", $indent) .
            $this->buildSingleToken($tokenSpec, $indent);
    }

    private function buildSingleToken(TokenSpec $tokenSpec, int $indent): string
    {
        return
            $this->buildMethodPart($tokenSpec->getCode(), $indent) .
            $this->buildOnToken($indent) . "\n" .
            $this->buildMethodPart("return true;", $indent);
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
            $line = str_repeat("    ", $indent);
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
     * @throws Exception
     */
    private function getDfa(string $context): Dfa
    {
        return $this->dfa[$context] ??= $this->buildDfa($context);
    }

    /**
     * @throws Exception
     */
    private function buildDfa(string $mode): Dfa
    {
        $nfa = new Nfa();
        $startState = $nfa->getStateMap()->createState();
        $nfa->getStateMap()->addStartState($startState);
        $nfaRegExpMap = [];
        /** @var Dfa[] $dfaList */
        $dfaList = [];
        foreach ($this->spec->getTokenSpecList($mode) as $tokenSpec) {
            $existingStates = $nfa->getStateMap()->getStateList();
            $regExpEntryState = $nfa->getStateMap()->createState();
            $nfa
                ->getEpsilonTransitionMap()
                ->addTransition($startState, $regExpEntryState, true);
            $this->buildRegExp($nfa, $regExpEntryState, $tokenSpec->getRegExp());
            $regExpStates = array_diff($nfa->getStateMap()->getStateList(), $existingStates);
            $nfaRegExpMap[$tokenSpec->getRegExp()] = $regExpStates;
            $dfaList[$tokenSpec->getRegExp()] = $this->buildIndependentRegExp($tokenSpec->getRegExp());
        }

        $joinedNfa = new Nfa();
        $startState = $joinedNfa->getStateMap()->createState();
        $joinedNfa->getStateMap()->addStartState($startState);
        $languageBuilder = LanguageBuilder::forNfa($joinedNfa);
        $joinedNfaStates = [];
        $nfaRegExpMap = [];
        $regExpFinishMap = [];
        foreach ($dfaList as $regExp => $dfa) {
            $nfaRegExpMap[$regExp] = [];
            foreach ($dfa->getStateMap()->getStateList() as $dfaState) {
                $nfaState = $joinedNfa->getStateMap()->createState();
                $nfaRegExpMap[$regExp][] = $nfaState;
                $joinedNfaStates[$dfaState] = $nfaState;
                if ($dfa->getStateMap()->isStartState($dfaState)) {
                    $joinedNfa->getEpsilonTransitionMap()->addTransition($startState, $nfaState, true);
                }
                if ($dfa->getStateMap()->isFinishState($dfaState)) {
                    $regExpFinishMap[$regExp][] = $nfaState;
                    $joinedNfa->getStateMap()->addFinishState($nfaState);
                }
            }
            foreach ($dfa->getTransitionMap()->getTransitionList() as $dfaStateIn => $transitions) {
                foreach ($transitions as $dfaStateOut => $symbols) {
                    foreach ($symbols as $symbol) {
                        $rangeSet = $dfa->getSymbolTable()->getRangeSet($symbol);
                        $newSymbols = $languageBuilder->getSymbolList(...$rangeSet->getRanges());
                        $oldSymbols = $joinedNfa
                            ->getSymbolTransitionMap()
                            ->transitionExists($joinedNfaStates[$dfaStateIn], $joinedNfaStates[$dfaStateOut])
                            ? $joinedNfa
                                ->getSymbolTransitionMap()
                                ->getTransition($joinedNfaStates[$dfaStateIn], $joinedNfaStates[$dfaStateOut])
                            : [];
                        $joinedNfa->getSymbolTransitionMap()->replaceTransition(
                            $joinedNfaStates[$dfaStateIn],
                            $joinedNfaStates[$dfaStateOut],
                            array_unique(array_merge($oldSymbols, $newSymbols)),
                        );
                    }
                }
            }
        }

        $dfa = new Dfa();
        (new DfaBuilder($dfa, $joinedNfa))->run();

        $dfaRegExpFinishMap = [];
        foreach ($dfa->getStateMap()->getFinishStateList() as $dfaFinishState) {
            $nfaFinishStates = array_intersect(
                $dfa->getStateMap()->getStateValue($dfaFinishState),
                $joinedNfa->getStateMap()->getFinishStateList()
            );
            foreach ($regExpFinishMap as $regExp => $regExpFinishStates) {
                foreach ($nfaFinishStates as $nfaFinishState) {
                    if (in_array($nfaFinishState, $regExpFinishStates)) {
                        $dfaRegExpFinishMap[$dfaFinishState] = (string) $regExp;
                        break 2;
                    }
                }
            }
        }
        foreach ($this->spec->getTokenSpecList($mode) as $tokenSpec) {
            if (!in_array($tokenSpec->getRegExp(), $dfaRegExpFinishMap)) {
                throw new Exception("Token not reachable for regular expression: {$tokenSpec->getRegExp()} ");
            }
        }
        $this->regExpFinishMap[$mode] = $dfaRegExpFinishMap;

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
        $nfaBuilder = new NfaBuilder($nfa, PropertyRangeLoader::create());
        $nfaBuilder->setStartState($entryState);
        (new Translator($tree, $nfaBuilder))->run();
    }

    /**
     * @throws Exception
     */
    private function buildIndependentRegExp(string $regExp): Dfa
    {
        $buffer = CharBufferFactory::createFromString($regExp);
        $tree = new Tree();
        ParserFactory::createFromBuffer($tree, $buffer)->run();
        $nfa = new Nfa();
        $nfaBuilder = new NfaBuilder($nfa, PropertyRangeLoader::create());
        (new Translator($tree, $nfaBuilder))->run();

        return DfaBuilder::fromNfa($nfa);
    }
}
