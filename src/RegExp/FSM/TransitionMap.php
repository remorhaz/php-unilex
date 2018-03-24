<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class TransitionMap
{

    private $stateMap;

    private $transitionMap = [];

    public function __construct(StateMapInterface $stateMap)
    {
        $this->stateMap = $stateMap;
    }

    /**
     * @param int $fromStateId
     * @param int $toStateId
     * @param $data
     * @throws Exception
     */
    public function addTransition(int $fromStateId, int $toStateId, $data): void
    {
        if ($this->transitionExists($fromStateId, $toStateId)) {
            throw new Exception("Transition {$fromStateId}->{$toStateId} is already added");
        }
        $this->replaceTransition($fromStateId, $toStateId, $data);
    }

    /**
     * @param int $fromStateId
     * @param int $toStateId
     * @param $data
     * @throws Exception
     */
    public function replaceTransition(int $fromStateId, int $toStateId, $data): void
    {
        [$validFromStateId, $validToStateId] = $this->getValidTransitionStates($fromStateId, $toStateId);
        $this->transitionMap[$validFromStateId][$validToStateId] = $data;
    }

    /**
     * @param int $fromStateId
     * @param int $toStateId
     * @return bool
     * @throws Exception
     */
    public function transitionExists(int $fromStateId, int $toStateId): bool
    {
        [$validFromStateId, $validToStateId] = $this->getValidTransitionStates($fromStateId, $toStateId);
        return isset($this->transitionMap[$validFromStateId][$validToStateId]);
    }

    /**
     * @param int $fromStateId
     * @param int $toStateId
     * @return mixed
     * @throws Exception
     */
    public function getTransition(int $fromStateId, int $toStateId)
    {
        if (!$this->transitionExists($fromStateId, $toStateId)) {
            throw new Exception("Transition {$fromStateId}->{$toStateId} is not defined");
        }
        [$validFromStateId, $validToStateId] = $this->getValidTransitionStates($fromStateId, $toStateId);
        return $this->transitionMap[$validFromStateId][$validToStateId];
    }

    /**
     * @param int $fromStateId
     * @param int $toStateId
     * @return array
     * @throws Exception
     */
    private function getValidTransitionStates(int $fromStateId, int $toStateId): array
    {
        if (!$this->stateMap->stateExists($fromStateId)) {
            throw new Exception("Invalid transition start state: {$fromStateId}");
        }
        if (!$this->stateMap->stateExists($toStateId)) {
            throw new Exception("Invalid transition finish state: {$toStateId}");
        }
        return [$fromStateId, $toStateId];
    }
}
