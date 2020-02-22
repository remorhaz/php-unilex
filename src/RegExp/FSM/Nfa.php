<?php

namespace Remorhaz\UniLex\RegExp\FSM;

class Nfa
{

    private $stateMap;

    private $epsilonTransitionMap;

    private $symbolTransitionMap;

    private $symbolTable;

    public function getStateMap(): StateMap
    {
        if (!isset($this->stateMap)) {
            $this->stateMap = new StateMap();
        }

        return $this->stateMap;
    }

    public function getEpsilonTransitionMap(): TransitionMap
    {
        if (!isset($this->epsilonTransitionMap)) {
            $this->epsilonTransitionMap = new TransitionMap($this->getStateMap());
        }

        return $this->epsilonTransitionMap;
    }

    public function getSymbolTransitionMap(): TransitionMap
    {
        if (!isset($this->symbolTransitionMap)) {
            $this->symbolTransitionMap = new TransitionMap($this->getStateMap());
        }

        return $this->symbolTransitionMap;
    }

    public function getSymbolTable(): SymbolTable
    {
        if (!isset($this->symbolTable)) {
            $this->symbolTable = new SymbolTable();
        }

        return $this->symbolTable;
    }
}
