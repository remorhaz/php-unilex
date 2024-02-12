<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use Closure;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\RegExp\FSM\StateMapInterface;
use Remorhaz\UniLex\RegExp\FSM\TransitionMap;

#[CoversClass(TransitionMap::class)]
class TransitionMapTest extends TestCase
{
    /**
     * @throws UniLexException
     */
    public function testAddTransition_FromStateNotExists_ThrowsException(): void
    {
        $stateExists = function (int $stateId): bool {
            return 1 == $stateId;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid transition start state: 0');
        $transitionMap->addTransition(0, 1, true);
    }

    /**
     * @throws UniLexException
     */
    public function testAddTransition_ToStateNotExists_ThrowsException(): void
    {
        $stateExists = function (int $stateId): bool {
            return 1 == $stateId;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid transition finish state: 0');
        $transitionMap->addTransition(1, 0, true);
    }

    /**
     * @throws UniLexException
     */
    public function testTransitionExists_FromStateNotExists_ThrowsException(): void
    {
        $stateExists = function (int $stateId): bool {
            return 1 == $stateId;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid transition start state: 0');
        $transitionMap->transitionExists(0, 1);
    }

    /**
     * @throws UniLexException
     */
    public function testTransitionExists_ToStateNotExists_ThrowsException(): void
    {
        $stateExists = function (int $stateId): bool {
            return 1 == $stateId;
        };
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid transition finish state: 0');
        $transitionMap->transitionExists(1, 0);
    }

    /**
     * @throws UniLexException
     */
    public function testTransitionExists_TransitionAdded_ReturnsTrue(): void
    {
        $stateExists = fn (): bool => true;
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $transitionMap->addTransition(1, 2, true);
        $actualValue = $transitionMap->transitionExists(1, 2);
        self::assertTrue($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testTransitionExists_TransitionNotAdded_ReturnsFalse(): void
    {
        $stateExists = fn (): bool => true;
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $actualValue = $transitionMap->transitionExists(1, 2);
        self::assertFalse($actualValue);
    }


    /**
     * @throws UniLexException
     */
    public function testGetTransition_FromStateNotExists_ThrowsException(): void
    {
        $stateExists = fn (int $stateId): bool => 1 == $stateId;
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid transition start state: 0');
        $transitionMap->addTransition(0, 1, true);
    }

    /**
     * @throws UniLexException
     */
    public function testGetTransition_ToStateNotExists_ThrowsException(): void
    {
        $stateExists = fn (int $stateId): bool => 1 == $stateId;
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid transition finish state: 0');
        $transitionMap->addTransition(1, 0, true);
    }

    /**
     * @throws UniLexException
     */
    public function testGetTransition_TransitionAdded_ReturnsMatchingData(): void
    {
        $stateExists = fn (): bool => true;
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $transitionMap->addTransition(1, 2, 3);
        $actualValue = $transitionMap->getTransition(1, 2);
        self::assertSame(3, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetTransition_TransitionNotAdded_ThrowsException(): void
    {
        $stateExists = fn (): bool => true;
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Transition 1->2 is not defined');
        $transitionMap->getTransition(1, 2);
    }

    /**
     * @throws UniLexException
     */
    public function testAddTransition_TransitionAdded_ThrowsException(): void
    {
        $stateExists = fn (): bool => true;
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $transitionMap->addTransition(1, 2, 3);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Transition 1->2 is already added');
        $transitionMap->addTransition(1, 2, 4);
    }

    /**
     * @throws UniLexException
     */
    public function testReplaceTransition_TransitionAdded_GetTransitionReturnsNewData(): void
    {
        $stateExists = fn (): bool => true;
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $transitionMap->addTransition(1, 2, 3);
        $transitionMap->replaceTransition(1, 2, 4);
        $actualValue = $transitionMap->getTransition(1, 2);
        self::assertSame(4, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testReplaceTransition_TransitionNotAdded_GetTransitionReturnsData(): void
    {
        $stateExists = fn (): bool => true;
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $transitionMap->replaceTransition(1, 2, 3);
        $actualValue = $transitionMap->getTransition(1, 2);
        self::assertSame(3, $actualValue);
    }

    public function testGetTransitionList_TransitionNotAdded_ReturnsEmptyArray(): void
    {
        $stateExists = fn (): bool => true;
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $actualValue = (new TransitionMap($stateMap))->getTransitionList();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetTransitionList_TransitionAdded_ReturnsArrayWithTransition(): void
    {
        $stateExists = fn (): bool => true;
        $stateMap = $this->createStateExistenceProvider($stateExists);
        $transitionMap = new TransitionMap($stateMap);
        $transitionMap->addTransition(1, 2, 3);
        $expectedValue = [1 => [2 => 3]];
        $actualValue = $transitionMap->getTransitionList();
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @param Closure $stateExists
     * @return StateMapInterface
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
