<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class DfaBuilder
{

    private $nfa;

    public function __construct(Nfa $nfa)
    {
        $this->nfa = $nfa;
    }

    public function getStateEpsilonMoves(int $stateIn): array
    {
        $moveList = $this
            ->nfa
            ->getEpsilonTransitionMap()
            ->findMoves($stateIn);
        $moveList[$stateIn] = true;
        return array_keys($moveList);
    }

    public function getStateEpsilonClosure(int ...$stateList): array
    {
        $notProcessedStateList = $stateList;
        $processedStateList = [];
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
