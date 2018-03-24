<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class StateMap implements StateMapInterface
{

    private $lastStateId = 0;

    private $stateList = [];

    private $startState;

    private $epsilonTransitionMap;

    private $rangeTransitionMap;

    public function __construct()
    {
        $this->epsilonTransitionMap = new TransitionMap($this);
        $this->rangeTransitionMap = new TransitionMap($this);
    }

    public function createState(): int
    {
        $stateId = ++$this->lastStateId;
        $this->stateList[$stateId] = true;
        return $stateId;
    }

    /**
     * @param int $stateId
     * @throws Exception
     */
    public function setStartState(int $stateId): void
    {
        if (isset($this->startState)) {
            throw new Exception("Start state is already set");
        }
        $this->startState = $this->getValidState($stateId);
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getStartState(): int
    {
        if (!isset($this->startState)) {
            throw new Exception("Start state is undefined");
        }
        return $this->startState;
    }

    public function stateExists(int $stateId): bool
    {
        return isset($this->stateList[$stateId]);
    }

    /**
     * @param int $fromStateId
     * @param int $toStateId
     * @throws Exception
     */
    public function addEpsilonTransition(int $fromStateId, int $toStateId): void
    {
        $this
            ->epsilonTransitionMap
            ->addTransition($fromStateId, $toStateId, true);
    }

    /**
     * @param int $fromStateId
     * @param int $toStateId
     * @return bool
     * @throws Exception
     */
    public function epsilonTransitionExists(int $fromStateId, int $toStateId): bool
    {
        return $this
            ->epsilonTransitionMap
            ->transitionExists($fromStateId, $toStateId);
    }

    /**
     * @param int $stateId
     * @return int
     * @throws Exception
     */
    private function getValidState(int $stateId): int
    {
        if (!$this->stateExists($stateId)) {
            throw new Exception("State {$stateId} is undefined");
        }
        return $stateId;
    }
}
