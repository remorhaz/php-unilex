<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class StateMap implements StateMapInterface
{

    private $lastStateId = 0;

    private $stateList = [];

    private $startState;

    private $finishStateList = [];

    /**
     * @param bool $value
     * @return int
     * @throws Exception
     */
    public function createState($value = true): int
    {
        if (is_null($value)) {
            throw new Exception("Null state value is not allowed");
        }
        $stateId = ++$this->lastStateId;
        $this->stateList[$stateId] = $value;
        return $stateId;
    }

    /**
     * @return int[]
     */
    public function getStateList(): array
    {
        return array_keys($this->stateList);
    }

    /**
     * @param int $stateId
     * @return mixed
     * @throws Exception
     */
    public function getStateValue(int $stateId)
    {
        return $this->stateList[$this->getValidState($stateId)];
    }

    /**
     * @param $value
     * @param int ...$stateList
     * @throws Exception
     */
    public function importState($value, int ...$stateList): void
    {
        foreach ($stateList as $stateId) {
            if (isset($this->stateList[$stateId])) {
                throw new Exception("State {$stateId} already exists");
            }
            $this->stateList[$stateId] = $value;
        }
        $this->lastStateId = max(array_keys($this->stateList));
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

    /**
     * @param int[] $stateList
     * @throws Exception
     */
    public function addFinishState(int ...$stateList): void
    {
        foreach ($stateList as $stateId) {
            $validStateId = $this->getValidState($stateId);
            if (isset($this->finishStateList[$validStateId])) {
                throw new Exception("Finish state {$validStateId} is already set");
            }
            $this->finishStateList[$validStateId] = $this->stateList[$validStateId];
        }
    }

    /**
     * @param int $stateId
     * @return bool
     * @throws Exception
     */
    public function isFinishState(int $stateId): bool
    {
        $validStateId = $this->getValidState($stateId);
        return isset($this->finishStateList[$validStateId]);
    }

    /**
     * @return int[]
     */
    public function getFinishStateList(): array
    {
        return array_keys($this->finishStateList);
    }

    public function stateExists(int $stateId): bool
    {
        return isset($this->stateList[$stateId]);
    }

    public function stateValueExists($value): bool
    {
        return false !== array_search($value, $this->stateList);
    }

    /**
     * @param $value
     * @return false|int|string
     * @throws Exception
     */
    public function getValueState($value)
    {
        $stateId = array_search($value, $this->stateList);
        if (false === $stateId) {
            throw new Exception("Value not found in state map");
        }
        return $stateId;
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
