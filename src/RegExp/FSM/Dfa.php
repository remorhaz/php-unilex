<?php

namespace Remorhaz\UniLex\RegExp\FSM;

class Dfa
{

    private $stateMap;

    private $symbolTable;

    private $transitionMap;

    public function getStateMap(): StateMap
    {
        if (!isset($this->stateMap)) {
            $this->stateMap = new StateMap;
        }
        return $this->stateMap;
    }

    public function getTransitionMap(): TransitionMap
    {
        if (!isset($this->transitionMap)) {
            $this->transitionMap = new TransitionMap($this->getStateMap());
        }
        return $this->transitionMap;
    }

    public function getSymbolTable(): SymbolTable
    {
        if (!isset($this->symbolTable)) {
            $this->symbolTable = new SymbolTable;
        }
        return $this->symbolTable;
    }
}
