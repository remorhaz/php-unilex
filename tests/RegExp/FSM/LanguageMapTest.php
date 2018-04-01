<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\LanguageMap;
use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\StateMapInterface;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\LanguageMap
 */
class LanguageMapTest extends TestCase
{

    public function testGetTransitionList_NoTransitionsAdded_ReturnsEmptyArray(): void
    {
        $actualValue = (new LanguageMap($this->createStateMap()))->getTranslationList();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testAddTransition_NoTransitionsAdded_GetTransitionListReturnsMatchingValue(): void
    {
        $languageMap = new LanguageMap($this->createStateMap());
        $languageMap->addTransition(1, 2, new Range(1, 2));
        $expectedValue = [1 => [2 => [0]]];
        $actualValue = $languageMap->getTranslationList();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testAddTransition_TransitionWithSameRangeAdded_GetTransitionListReturnsMatchingValue(): void
    {
        $languageMap = new LanguageMap($this->createStateMap());
        $languageMap->addTransition(1, 2, new Range(1, 2));
        $languageMap->addTransition(1, 3, new Range(1, 2));
        $expectedValue = [1 => [2 => [0], 3 => [0]]];
        $actualValue = $languageMap->getTranslationList();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testAddTransition_TransitionWithNotIntersectingRangeAdded_GetTransitionListReturnsMatchingValue(): void
    {
        $languageMap = new LanguageMap($this->createStateMap());
        $languageMap->addTransition(1, 2, new Range(1, 2));
        $languageMap->addTransition(1, 3, new Range(3, 4));
        $expectedValue = [1 => [2 => [0], 3 => [1]]];
        $actualValue = $languageMap->getTranslationList();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testAddTransition_TransitionWithIntersectingRangeAdded_GetTransitionListReturnsMatchingValue(): void
    {
        $languageMap = new LanguageMap($this->createStateMap());
        $languageMap->addTransition(1, 2, new Range(1, 2));
        $languageMap->addTransition(1, 3, new Range(2, 4));
        $expectedValue = [1 => [2 => [0, 1], 3 => [1, 2]]];
        $actualValue = $languageMap->getTranslationList();
        self::assertEquals($expectedValue, $actualValue);
    }

    private function createStateMap(): StateMapInterface
    {
        return new class implements StateMapInterface {

            public function stateExists(int $stateId): bool
            {
                return true;
            }
        };
    }
}
