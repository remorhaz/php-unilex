<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

use function array_key_first;
use function array_keys;
use function count;

class StateMap implements StateMapInterface
{
    private int $lastStateId = 0;

    /**
     * @var array<int, mixed>
     */
    private array $stateList = [];

    /**
     * @var array<int, mixed>
     */
    private array $startStateList = [];

    /**
     * @var array<int, mixed>
     */
    private array $finishStateList = [];

    /**
     * @param mixed $value
     * @return int
     * @throws Exception
     */
    public function createState(mixed $value = true): int
    {
        if (is_null($value)) {
            throw new Exception("Null state value is not allowed");
        }
        $stateId = ++$this->lastStateId;
        $this->stateList[$stateId] = $value;

        return $stateId;
    }

    /**
     * @return list<int>
     */
    public function getStateList(): array
    {
        return array_keys($this->stateList);
    }

    /**
     * @throws Exception
     */
    public function getStateValue(int $stateId): mixed
    {
        return $this->stateList[$this->getValidState($stateId)];
    }

    /**
     * @throws Exception
     */
    public function importState(mixed $value, int ...$stateList): void
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
     * @param int ...$stateList
     * @throws Exception
     */
    public function addStartState(int ...$stateList): void
    {
        foreach ($stateList as $stateId) {
            $validStateId = $this->getValidState($stateId);
            $this->startStateList[$validStateId] = isset($this->startStateList[$validStateId])
                ? throw new Exception("Start state $validStateId is already set")
                : $this->stateList[$validStateId];
        }
    }

    /**
     * @throws Exception
     */
    public function getStartState(): int
    {
        return count($this->startStateList) == 1
            ? array_key_first($this->startStateList)
            : throw new Exception("Start state is undefined");
    }

    /**
     * @return list<int>
     */
    public function getStartStateList(): array
    {
        return array_keys($this->startStateList);
    }

    /**
     * @throws Exception
     */
    public function replaceStartStateList(int ...$stateIdList): void
    {
        $this->startStateList = [];
        $this->addStartState(...$stateIdList);
    }

    /**
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
     * @throws Exception
     */
    public function isFinishState(int $stateId): bool
    {
        $validStateId = $this->getValidState($stateId);

        return isset($this->finishStateList[$validStateId]);
    }

    /**
     * @throws Exception
     */
    public function isStartState(int $stateId): bool
    {
        $validStateId = $this->getValidState($stateId);

        return isset($this->startStateList[$validStateId]);
    }

    /**
     * @return list<int>
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
     * @throws Exception
     */
    public function getValueState(mixed $value): int
    {
        $stateId = array_search($value, $this->stateList);

        return false === $stateId
            ? throw new Exception("Value not found in state map")
            : $stateId;
    }

    /**
     * @throws Exception
     */
    private function getValidState(int $stateId): int
    {
        return $this->stateExists($stateId)
            ? $stateId
            : throw new Exception("State $stateId is undefined");
    }
}
