<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class StateMap implements StateMapInterface
{

    private $lastStateId = 0;

    private $stateList = [];

    private $startState;

    public function createState($value = true): int
    {
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
