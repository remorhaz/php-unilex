<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception;

class StateMap implements StateMapInterface
{

    private $lastStateId = 0;

    private $stateList = [];

    private $startState;

    private $epsilonTransitionMap;

    private $rangeTransitionMap;

    /**
     * @param Tree $tree
     * @return StateMap
     * @throws Exception
     */
    public static function buildFromTree(Tree $tree): self
    {
        $stateMap = new self;
        (new Translator($tree, new StateMapBuilder($stateMap)))->run();
        return $stateMap;
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
            ->getEpsilonTransitionMap()
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
            ->getEpsilonTransitionMap()
            ->transitionExists($fromStateId, $toStateId);
    }

    /**
     * @param int $fromStateId
     * @param int $toStateId
     * @param int $char
     * @throws Exception
     */
    public function addCharTransition(int $fromStateId, int $toStateId, int $char): void
    {
        $this->addRangeTransition($fromStateId, $toStateId, $char, $char);
    }

    /**
     * @param int $fromStateId
     * @param int $toStateId
     * @param int $startChar
     * @param int $finishChar
     * @throws Exception
     */
    public function addRangeTransition(int $fromStateId, int $toStateId, int $startChar, int $finishChar): void
    {
        $range = [$startChar, $finishChar];
        $transitionExists = $this
            ->getRangeTransitionMap()
            ->transitionExists($fromStateId, $toStateId);
        $rangeList = $transitionExists
            ? $this
                ->getRangeTransitionMap()
                ->getTransition($fromStateId, $toStateId)
            : [];
        // TODO: Optimize merging ranges.
        $rangeList[] = $range;
        $this
            ->getRangeTransitionMap()
            ->replaceTransition($fromStateId, $toStateId, $rangeList);
    }

    /**
     * @param int $fromStateId
     * @param int $toStateId
     * @param int $char
     * @return bool
     * @throws Exception
     */
    public function charTransitionExists(int $fromStateId, int $toStateId, int $char): bool
    {
        $transitionExists = $this
            ->getRangeTransitionMap()
            ->transitionExists($fromStateId, $toStateId);
        if (!$transitionExists) {
            return false;
        }
        $rangeList = $this
            ->getRangeTransitionMap()
            ->getTransition($fromStateId, $toStateId);
        foreach ($rangeList as $range) {
            [$startChar, $finishChar] = $range;
            if ($char >= $startChar && $char <= $finishChar) {
                return true;
            }
        }
        return false;
    }

    public function getCharTransitionList(): array
    {
        return $this
            ->getRangeTransitionMap()
            ->getTransitionList();
    }

    public function getEpsilonTransitionList(): array
    {
        return $this
            ->getEpsilonTransitionMap()
            ->getTransitionList();
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

    private function getEpsilonTransitionMap(): TransitionMap
    {
        if (!isset($this->epsilonTransitionMap)) {
            $this->epsilonTransitionMap = new TransitionMap($this);
        }
        return $this->epsilonTransitionMap;
    }

    private function getRangeTransitionMap(): TransitionMap
    {
        if (!isset($this->rangeTransitionMap)) {
            $this->rangeTransitionMap = new TransitionMap($this);
        }
        return $this->rangeTransitionMap;
    }
}
