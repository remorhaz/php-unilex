<?php

namespace Remorhaz\UniLex\RegExp\FSM;

class DfaBuilder
{

    private $dfa;

    private $nfa;

    private $nfaCalc;

    public function __construct(Dfa $dfa, Nfa $nfa)
    {
        $this->dfa = $dfa;
        $this->nfa = $nfa;
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function run(): void
    {
        $dfaStateMap = [];
        $notProcessedStateList = [];
        $startStateId = $this->nfa->getStateMap()->getStartState();
        $nfaStateList = $this->getNfaCalc()->getEpsilonClosure($startStateId);
        $startState = $this->dfa->getStateMap()->createState();
        $dfaStateMap[$startState] = $nfaStateList;
        $this->dfa->getStateMap()->setStartState($startState);
        $notProcessedStateList[] = [$startState, $nfaStateList];
        // TODO: implement copy method in symbol table
        foreach ($this->nfa->getSymbolTable()->getRangeSetList() as $symbolId => $rangeSet) {
            $this->dfa->getSymbolTable()->addSymbol(clone $rangeSet);
        }
        while (!empty($notProcessedStateList)) {
            [$stateIn, $nfaStateList] = array_pop($notProcessedStateList);
            foreach ($this->dfa->getSymbolTable()->getRangeSetList() as $symbolId => $rangeSet) {
                $symbolMoves = $this->getNfaCalc()->getSymbolMoves($symbolId, ...$nfaStateList);
                if (empty($symbolMoves)) {
                    continue;
                }
                $nextStateList = $this->getNfaCalc()->getEpsilonClosure(...$symbolMoves);
                $stateOut = array_search($nextStateList, $dfaStateMap);
                if (false === $stateOut) {
                    $stateOut = $this->dfa->getStateMap()->createState();
                    $dfaStateMap[$stateOut] = $nextStateList;
                    $notProcessedStateList[] = [$stateOut, $nextStateList];
                }
                $transitionExists = $this->dfa->getTransitionMap()->transitionExists($stateIn, $stateOut);
                $symbolList = $transitionExists
                    ? $this
                        ->dfa
                        ->getTransitionMap()
                        ->getTransition($stateIn, $stateOut)
                    : [];
                $symbolList[] = $symbolId;
                $this->dfa->getTransitionMap()->replaceTransition($stateIn, $stateOut, $symbolList);
            }
        }
    }

    private function getNfaCalc(): NfaCalc
    {
        if (!isset($this->nfaCalc)) {
            $this->nfaCalc = new NfaCalc($this->nfa);
        }
        return $this->nfaCalc;
    }
}
