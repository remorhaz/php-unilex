<?php

namespace Remorhaz\UniLex\RegExp\FSM;

class DfaBuilder
{

    private $nfa;

    public function __construct(Nfa $nfa)
    {
        $this->nfa = $nfa;
    }

    public function getStateEpsilonMoves(int $state): array
    {
        $transitionList = $this
            ->nfa
            ->getEpsilonTransitionMap()
            ->getTransitionList();
        $result = array_keys($transitionList[$state] ?? []);
        if (!in_array($state, $result)) {
            $result[] = $state;
        }
        return $result;
    }

    public function getStateEpsilonClosure(int $state): array
    {
        $processedStateList = [];
        $notProcessedStateList = [$state];
        while (!empty($notProcessedStateList)) {
            $nextState = array_pop($notProcessedStateList);
            $processedStateList[] = $nextState;
            $stateEpsilonMoves = $this->getStateEpsilonMoves($nextState);
            $newNotProcessedStateList = array_diff($stateEpsilonMoves, $processedStateList, $notProcessedStateList);
            $notProcessedStateList = array_merge($notProcessedStateList, $newNotProcessedStateList);
        }
        return $processedStateList;
    }
}
