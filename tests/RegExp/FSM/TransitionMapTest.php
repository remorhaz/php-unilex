<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use Closure;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\StateMapInterface;
use Remorhaz\UniLex\RegExp\FSM\TransitionMap;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\TransitionMap
 */
class TransitionMapTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid transition start state: 0
     */
    public function testAddTransition_FromStateNotExists_ThrowsException(): void
    {
        $stateExists = function (int $stateId): bool {
            return 1 == $stateId;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        (new TransitionMap($stateMap))->addTransition(0, 1, true);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid transition finish state: 0
     * @throws \ReflectionException
     */
    public function testAddTransition_ToStateNotExists_ThrowsException(): void
    {
        $stateExists = function (int $stateId): bool {
            return 1 == $stateId;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        (new TransitionMap($stateMap))->addTransition(1, 0, true);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid transition start state: 0
     * @throws \ReflectionException
     */
    public function testTransitionExists_FromStateNotExists_ThrowsException(): void
    {
        $stateExists = function (int $stateId): bool {
            return 1 == $stateId;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        (new TransitionMap($stateMap))->transitionExists(0, 1);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid transition finish state: 0
     * @throws \ReflectionException
     */
    public function testTransitionExists_ToStateNotExists_ThrowsException(): void
    {
        $stateExists = function (int $stateId): bool {
            return 1 == $stateId;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        (new TransitionMap($stateMap))->transitionExists(1, 0);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
     */
    public function testTransitionExists_TransitionAdded_ReturnsTrue(): void
    {
        $stateExists = function (): bool {
            return true;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $transitionMap->addTransition(1, 2, true);
        $actualValue = $transitionMap->transitionExists(1, 2);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
     */
    public function testTransitionExists_TransitionNotAdded_ReturnsFalse(): void
    {
        $stateExists = function (): bool {
            return true;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $actualValue = $transitionMap->transitionExists(1, 2);
        self::assertFalse($actualValue);
    }


    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid transition start state: 0
     * @throws \ReflectionException
     */
    public function testGetTransition_FromStateNotExists_ThrowsException(): void
    {
        $stateExists = function (int $stateId): bool {
            return 1 == $stateId;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        (new TransitionMap($stateMap))->addTransition(0, 1, true);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid transition finish state: 0
     * @throws \ReflectionException
     */
    public function testGetTransition_ToStateNotExists_ThrowsException(): void
    {
        $stateExists = function (int $stateId): bool {
            return 1 == $stateId;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        (new TransitionMap($stateMap))->addTransition(1, 0, true);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
     */
    public function testGetTransition_TransitionAdded_ReturnsMatchingData(): void
    {
        $stateExists = function (): bool {
            return true;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $transitionMap->addTransition(1, 2, 3);
        $actualValue = $transitionMap->getTransition(1, 2);
        self::assertSame(3, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Transition 1->2 is not defined
     * @throws \ReflectionException
     */
    public function testGetTransition_TransitionNotAdded_ThrowsException(): void
    {
        $stateExists = function (): bool {
            return true;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        (new TransitionMap($stateMap))->getTransition(1, 2);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Transition 1->2 is already added
     * @throws \ReflectionException
     */
    public function testAddTransition_TransitionAdded_ThrowsException(): void
    {
        $stateExists = function (): bool {
            return true;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $transitionMap->addTransition(1, 2, 3);
        $transitionMap->addTransition(1, 2, 4);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
     */
    public function testReplaceTransition_TransitionAdded_GetTransitionReturnsNewData(): void
    {
        $stateExists = function (): bool {
            return true;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $transitionMap->addTransition(1, 2, 3);
        $transitionMap->replaceTransition(1, 2, 4);
        $actualValue = $transitionMap->getTransition(1, 2);
        self::assertSame(4, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \ReflectionException
     */
    public function testReplaceTransition_TransitionNotAdded_GetTransitionReturnsData(): void
    {
        $stateExists = function (): bool {
            return true;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $transitionMap->replaceTransition(1, 2, 3);
        $actualValue = $transitionMap->getTransition(1, 2);
        self::assertSame(3, $actualValue);
    }

    /**
     * @param Closure $stateExists
     * @return StateMapInterface
     * @throws \ReflectionException
     */
    private function createStateExistenceProvider(Closure $stateExists): StateMapInterface
    {
        $stateMap = $this->createMock(StateMapInterface::class);
        $stateMap
            ->method('stateExists')
            ->willReturnCallback($stateExists);
        /** @var StateMapInterface $stateMap */
        return $stateMap;
    }
}
