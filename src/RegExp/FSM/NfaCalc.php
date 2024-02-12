<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\FSM;

class NfaCalc
{
    public function __construct(
        private readonly Nfa $nfa,
    ) {
    }

    /**
     * @param int ...$stateList
     * @return list<int>
     */
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

    /**
     * @param int $symbolId
     * @param int ...$stateList
     * @return list<int>
     */
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

    /**
     * @param int $symbolId
     * @param int $stateIn
     * @return list<int>
     */
    private function getStateSymbolMoves(int $symbolId, int $stateIn): array
    {
        $moveList = $this
            ->nfa
            ->getSymbolTransitionMap()
            ->getExitList($stateIn);
        $stateOutList = [];
        foreach ($moveList as $stateOut => $symbolList) {
            if (in_array($symbolId, $symbolList)) {
                $stateOutList[] = $stateOut;
            }
        }

        return $stateOutList;
    }

    /**
     * @return list<int>
     */
    private function getStateEpsilonMoves(int $stateIn): array
    {
        $moveList = $this
            ->nfa
            ->getEpsilonTransitionMap()
            ->getExitList($stateIn);
        $moveList[$stateIn] = true;

        return array_keys($moveList);
    }
}
