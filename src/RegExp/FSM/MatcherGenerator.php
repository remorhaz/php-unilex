<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class MatcherGenerator
{

    private $dfa;

    private $output;

    public function __construct(Dfa $dfa)
    {
        $this->dfa = $dfa;
    }

    /**
     * @throws Exception
     */
    public function generate(): void
    {
        $statementList = [];
        $statementList[] = "goto state{$this->dfa->getStateMap()->getStartState()};";
        foreach ($this->dfa->getStateMap()->getStateList() as $stateIn) {
            $statementList[] = "state{$stateIn}:";
            foreach ($this->dfa->getTransitionMap()->getExitList($stateIn) as $stateOut => $symbolList) {
                foreach ($symbolList as $symbol) {
                    $rangeSet = $this->dfa->getSymbolTable()->getRangeSet($symbol);
                    $conditionList = [];
                    $statementList[] = "\$char = \$buffer->getSymbol();";
                    foreach ($rangeSet->getRanges() as $range) {
                        $conditionList[] = $range->getStart() == $range->getFinish()
                            ? "\$char == {$range->getStart()}"
                            : "{$range->getStart()} <= \$char && \$char <= {$range->getFinish()}";
                    }
                    $condition = implode(" || ", $conditionList);
                    $statementList[] = "if ({$condition}) {";
                    $statementList[] = "    \$buffer->nextSymbol();";
                    $statementList[] = "    goto state{$stateOut};";
                    $statementList[] = "}";
                }
            }
            $statementList[] = $this->dfa->getStateMap()->isFinishState($stateIn)
                ? "goto finish;"
                : "goto error;";
        }
        $statementList[] = "error:";
        $statementList[] = "return false;";
        $statementList[] = "finish:";
        $statementList[] = "return true;";
        $this->output = implode(PHP_EOL, $statementList);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getOutput(): string
    {
        if (!isset($this->output)) {
            throw new Exception("Output is not defined");
        }
        return $this->output;
    }
}
