<?php

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\RegExp\FSM\Dfa;
use Remorhaz\UniLex\RegExp\FSM\DfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\Nfa;
use Remorhaz\UniLex\RegExp\FSM\NfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;
use Remorhaz\UniLex\RegExp\ParserFactory;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

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
    public function buildOutput(): string
    {
        return
            "<?php\n{$this->buildFileComment()}\n" .
            "namespace {$this->spec->getTargetNamespaceName()};\n" .
            "{$this->buildUseList()}\n" .
            "class {$this->spec->getTargetShortName()} extends {$this->spec->getTemplateClass()->getShortName()}\n" .
            "{\n" .
            "\n" .
            "    public function match({$this->buildMatchParameters()}): bool\n" .
            "    {\n{$this->buildMatchBody()}" .
            "    }\n" .
            "}\n";
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
    private function buildUseList(): string
    {
        $result = '';
        foreach ($this->spec->getUsedClassList() as $alias => $className) {
            $classWithAlias = is_string($alias) ? "{$className} {$alias}" : $className;
            $result .= "use {$classWithAlias};\n";
        }
        return $result == '' ? $result : "\n{$result}";
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
        return
            $this->buildBeforeMatch() .
            $this->buildFsmEntry() .
            $this->buildFsmMoves() .
            $this->buildErrorState();
    }

    private function buildBeforeMatch(): string
    {
        $code = $this->spec->getBeforeMatch();
        return $this->buildMethodPart($code);
    }

    /**
     * @return string
     * @throws Exception
     */
    private function buildFsmEntry(): string
    {
        return $this->buildMethodPart("goto state{$this->getDfa()->getStateMap()->getStartState()};\n");
    }

    /**
     * @return string
     * @throws Exception
     */
    private function buildFsmMoves(): string
    {
        $result = '';
        foreach ($this->getDfa()->getStateMap()->getStateList() as $stateIn) {
            $result .=
                $this->buildStateEntry($stateIn) .
                $this->buildStateTransitionList($stateIn) .
                $this->buildStateFinish($stateIn);
        }

        return $result;
    }

    /**
     * @param int $stateIn
     * @return string
     * @throws Exception
     */
    private function buildStateEntry(int $stateIn): string
    {
        $result = '';
        $result .= $this->buildMethodPart("state{$stateIn}:");
        $moves = $this->getDfa()->getTransitionMap()->findMoves($stateIn);
        if (empty($moves)) {
            return $result;
        }
        $result .= $this->buildMethodPart("if (\$buffer->isEnd()) {");
        $result .= $this->getDfa()->getStateMap()->isFinishState($stateIn)
            ? $this->buildMethodPart("goto finish{$stateIn};", 3)
            : $this->buildMethodPart("goto error;", 3);
        $result .=
            $this->buildMethodPart("}") .
            $this->buildMethodPart("\$char = \$buffer->getSymbol();");
        return $result;
    }

    /**
     * @param int $stateIn
     * @return string
     * @throws Exception
     */
    private function buildStateTransitionList(int $stateIn): string
    {
        $result = '';
        foreach ($this->getDfa()->getTransitionMap()->findMoves($stateIn) as $stateOut => $symbolList) {
            foreach ($symbolList as $symbol) {
                $rangeSet = $this->getDfa()->getSymbolTable()->getRangeSet($symbol);
                $result .=
                    $this->buildMethodPart("if ({$this->buildRangeSetCondition($rangeSet)}) {") .
                    $this->buildOnTransition() .
                    $this->buildMethodPart("\$buffer->nextSymbol();", 3) .
                    $this->buildMethodPart("goto state{$stateOut};", 3) .
                    $this->buildMethodPart("}");
            }
        }
        return $result;
    }

    private function buildHex(int $char): string
    {
        $hexChar = strtoupper(dechex($char));
        if (strlen($hexChar) % 2 != 0) {
            $hexChar = "0{$hexChar}";
        }
        return "0x{$hexChar}";
    }

    private function buildRangeSetCondition(RangeSet $rangeSet): string
    {
        $conditionList = [];
        foreach ($rangeSet->getRanges() as $range) {
            $startChar = $this->buildHex($range->getStart());
            if ($range->getStart() == $range->getFinish()) {
                $conditionList[] = "{$startChar} == \$char";
                continue;
            }
            $finishChar = $this->buildHex($range->getFinish());
            if ($range->getStart() + 1 == $range->getFinish()) {
                $conditionList[] = "{$startChar} == \$char";
                $conditionList[] = "{$finishChar} == \$char";
                continue;
            }
            $conditionList[] = "{$startChar} <= \$char && \$char <= {$finishChar}";
        }
        $result = implode(" || ", $conditionList);
        if (strlen($result) + 15 <= 120) {
            return $result;
        }
        $result = $this->buildMethodPart(implode(" ||\n", $conditionList), 1);
        return ltrim($result);
    }

    /**
     * @param int $stateIn
     * @return string
     * @throws Exception
     */
    private function buildStateFinish(int $stateIn): string
    {
        if (!$this->getDfa()->getStateMap()->isFinishState($stateIn)) {
            return $this->buildMethodPart("goto error;\n");
        }
        $result = '';
        if (!empty($this->getDfa()->getTransitionMap()->findMoves($stateIn))) {
            $result .= $this->buildMethodPart("finish{$stateIn}:");
        }
        $tokenSpec = $this->getTokenSpec($stateIn);
        $result .=
            $this->buildMethodPart("\$tokenType = {$tokenSpec->getTokenType()};") .
            $this->buildOnToken() .
            $this->buildMethodPart($tokenSpec->getCode()) .
            $this->buildMethodPart("return true;\n");
        return $result;
    }

    /**
     * @param int $stateIn
     * @return TokenSpec
     * @throws Exception
     */
    private function getTokenSpec(int $stateIn): TokenSpec
    {
        $tokenSpec = null;
        foreach ($this->getDfa()->getStateMap()->getStateValue($stateIn) as $nfaFinishState) {
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

    private function buildOnToken(): string
    {
        return $this->buildMethodPart($this->spec->getOnToken());
    }

    /**
     * @return Dfa
     * @throws Exception
     */
    private function getDfa(): Dfa
    {
        if (!isset($this->dfa)) {
            $this->dfa = $this->buildDfa();
        }
        return $this->dfa;
    }

    /**
     * @return Dfa
     * @throws Exception
     */
    private function buildDfa(): Dfa
    {
        $nfa = new Nfa;
        $startState = $nfa->getStateMap()->createState();
        $nfa->getStateMap()->setStartState($startState);
        $oldFinishStates = [];
        $this->tokenNfaStateMap = [];
        foreach ($this->spec->getTokenSpecList() as $tokenSpec) {
            $regExpEntryState = $nfa->getStateMap()->createState();
            $nfa
                ->getEpsilonTransitionMap()
                ->addTransition($startState, $regExpEntryState, true);
            $buffer = CharBufferFactory::createFromUtf8String($tokenSpec->getRegExp());
            $tree = new Tree;
            ParserFactory::createFromBuffer($tree, $buffer)->run();
            $nfaBuilder = new NfaBuilder($nfa);
            $nfaBuilder->setStartState($regExpEntryState);
            (new Translator($tree, $nfaBuilder))->run();
            $finishStates = $nfa->getStateMap()->getFinishStateList();
            $newFinishStates = array_diff($finishStates, $oldFinishStates);
            foreach ($newFinishStates as $finishState) {
                $this->tokenNfaStateMap[$finishState] = $tokenSpec;
            }
            $oldFinishStates = $finishStates;
        }
        return DfaBuilder::fromNfa($nfa);
    }
}
