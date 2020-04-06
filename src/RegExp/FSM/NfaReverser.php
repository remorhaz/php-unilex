<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\FSM;

final class NfaReverser
{

    public function reverseNfa(Nfa $sourceNfa): Nfa
    {
        $targetNfa = new Nfa();
        $targetNfa->setSymbolTable($sourceNfa->getSymbolTable());
        $sourceStateMap = $sourceNfa->getStateMap();
        $targetStateMap = $targetNfa->getStateMap();
        foreach ($sourceStateMap->getStateList() as $stateId) {
            $targetStateMap->importState($sourceStateMap->getStateValue($stateId), $stateId);
        }
        $targetStateMap->addFinishState(...$sourceStateMap->getStartStateList());
        $targetStateMap->addStartState(...$sourceStateMap->getFinishStateList());

        foreach ($sourceNfa->getEpsilonTransitionMap()->getTransitionList() as $sourceStateId => $targetStates) {
            foreach ($targetStates as $targetStateId => $value) {
                $targetNfa->getEpsilonTransitionMap()->addTransition($targetStateId, $sourceStateId, $value);
            }
        }

        foreach ($sourceNfa->getSymbolTransitionMap()->getTransitionList() as $sourceStateId => $targetStates) {
            foreach ($targetStates as $targetStateId => $value) {
                $targetNfa->getSymbolTransitionMap()->addTransition($targetStateId, $sourceStateId, $value);
            }
        }

        return $targetNfa;
    }

    public function reverseDfa(Dfa $sourceDfa): Nfa
    {
        $targetNfa = new Nfa();
        $targetNfa->setSymbolTable($sourceDfa->getSymbolTable());
        $sourceStateMap = $sourceDfa->getStateMap();
        $targetStateMap = $targetNfa->getStateMap();
        foreach ($sourceStateMap->getStateList() as $stateId) {
            $targetStateMap->importState($sourceStateMap->getStateValue($stateId), $stateId);
        }
        $targetStateMap->addFinishState(...$sourceStateMap->getStartStateList());
        $targetStateMap->addStartState(...$sourceStateMap->getFinishStateList());

        foreach ($sourceDfa->getTransitionMap()->getTransitionList() as $sourceStateId => $targetStates) {
            foreach ($targetStates as $targetStateId => $value) {
                $targetNfa->getSymbolTransitionMap()->addTransition($targetStateId, $sourceStateId, $value);
            }
        }

        return $targetNfa;
    }
}
