<?php

namespace Remorhaz\UniLex\RegExp\FSM;

class NfaCalc
{

    private $nfa;

    public function __construct(Nfa $nfa)
    {
        $this->nfa = $nfa;
    }

    public function getEpsilonClosure(int ...$stateList): array
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
        sort($processedStateList);
        return $processedStateList;
    }

    public function getSymbolMoves(int $symbolId, int ...$stateList): array
    {
        $result = [];
        foreach ($stateList as $stateId) {
            $moveList = $this->getStateSymbolMoves($symbolId, $stateId);
            $newMoveList = array_diff($moveList, $result);
            $result = array_merge($result, $newMoveList);
        }
        sort($result);
        return $result;
    }

    private function getStateSymbolMoves(int $symbolId, int $stateIn): array
    {
        $moveList = $this
            ->nfa
            ->getSymbolTransitionMap()
            ->findMoves($stateIn);
        $stateOutList = [];
        foreach ($moveList as $stateOut => $symbolList) {
            if (in_array($symbolId, $symbolList)) {
                $stateOutList[] = $stateOut;
            }
        }
        return $stateOutList;
    }

    private function getStateEpsilonMoves(int $stateIn): array
    {
        $moveList = $this
            ->nfa
            ->getEpsilonTransitionMap()
            ->findMoves($stateIn);
        $moveList[$stateIn] = true;
        return array_keys($moveList);
    }
}
