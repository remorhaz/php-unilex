<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class Dfa
{
    private ?StateMap $stateMap = null;

    private ?SymbolTable $symbolTable = null;

    private ?TransitionMap $transitionMap = null;

    public function getStateMap(): StateMap
    {
        return $this->stateMap ??= new StateMap();
    }

    public function getTransitionMap(): TransitionMap
    {
        return $this->transitionMap ??= new TransitionMap($this->getStateMap());
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
