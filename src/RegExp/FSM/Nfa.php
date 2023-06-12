<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class Nfa
{
    private ?StateMap $stateMap = null;

    private ?TransitionMap $epsilonTransitionMap = null;

    private ?TransitionMap $symbolTransitionMap = null;

    private ?SymbolTable $symbolTable = null;

    public function getStateMap(): StateMap
    {
        return $this->stateMap ??= new StateMap();
    }

    public function getEpsilonTransitionMap(): TransitionMap
    {
        return $this->epsilonTransitionMap ??= new TransitionMap($this->getStateMap());
    }

    public function getSymbolTransitionMap(): TransitionMap
    {
        return $this->symbolTransitionMap ??= new TransitionMap($this->getStateMap());
    }

    public function getSymbolTable(): SymbolTable
    {
        return $this->symbolTable ??= new SymbolTable();
    }

    /**
     * @throws Exception
     */
    public function setSymbolTable(SymbolTable $symbolTable): void
    {
        $this->symbolTable = isset($this->symbolTable)
            ? throw new Exception("Symbol table already exists in DFA")
            : $symbolTable;
    }
}
