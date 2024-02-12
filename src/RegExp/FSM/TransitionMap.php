<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class TransitionMap
{
    /**
     * @var array<int, array<int, mixed>>
     */
    private array $transitionMap = [];

    public function __construct(
        private readonly StateMapInterface $stateMap,
    ) {
    }

    /**
     * @throws Exception
     */
    public function addTransition(int $fromStateId, int $toStateId, mixed $data): void
    {
        if ($this->transitionExists($fromStateId, $toStateId)) {
            throw new Exception("Transition $fromStateId->$toStateId is already added");
        }
        $this->replaceTransition($fromStateId, $toStateId, $data);
    }

    /**
     * @throws Exception
     */
    public function replaceTransition(int $fromStateId, int $toStateId, mixed $data): void
    {
        [$validFromStateId, $validToStateId] = $this->getValidTransitionStates($fromStateId, $toStateId);
        $this->transitionMap[$validFromStateId][$validToStateId] = $data;
    }

    /**
     * @throws Exception
     */
    public function transitionExists(int $fromStateId, int $toStateId): bool
    {
        [$validFromStateId, $validToStateId] = $this->getValidTransitionStates($fromStateId, $toStateId);

        return isset($this->transitionMap[$validFromStateId][$validToStateId]);
    }

    /**
     * @throws Exception
     */
    public function getTransition(int $fromStateId, int $toStateId): mixed
    {
        if (!$this->transitionExists($fromStateId, $toStateId)) {
            throw new Exception("Transition {$fromStateId}->{$toStateId} is not defined");
        }
        [$validFromStateId, $validToStateId] = $this->getValidTransitionStates($fromStateId, $toStateId);

        return $this->transitionMap[$validFromStateId][$validToStateId];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function getTransitionList(): array
    {
        return $this->transitionMap;
    }

    /**
     * @param int $stateIn
     * @return array<int, mixed>
     */
    public function getExitList(int $stateIn): array
    {
        return $this->transitionMap[$stateIn] ?? [];
    }

    /**
     * @param int $stateOut
     * @return array<int, mixed>
     */
    public function getEnterList(int $stateOut): array
    {
        $result = [];
        foreach ($this->transitionMap as $stateIn => $stateOutMap) {
            if (isset($stateOutMap[$stateOut])) {
                $result[$stateIn] = $stateOutMap[$stateOut];
            }
        }
        return $result;
    }

    /**
     * @param callable(mixed, int, int):void $callback
     * @return void
     */
    public function onEachTransition(callable $callback): void
    {
        foreach ($this->transitionMap as $stateIn => $stateOutMap) {
            foreach ($stateOutMap as $stateOut => $data) {
                ($callback)($data, $stateIn, $stateOut);
            }
        }
    }

    /**
     * @param callable(mixed, int, int):mixed $callback
     * @return void
     */
    public function replaceEachTransition(callable $callback): void
    {
        $replaceCallback = function ($data, int $stateIn, int $stateOut) use ($callback) {
            $newData = ($callback)($data, $stateIn, $stateOut);
            $this->replaceTransition($stateIn, $stateOut, $newData);
        };
        $this->onEachTransition($replaceCallback);
    }

    /**
     * @param int $fromStateId
     * @param int $toStateId
     * @return array{int, int}
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
