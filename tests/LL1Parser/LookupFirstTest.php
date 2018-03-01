<?php

namespace Remorhaz\UniLex\Test\LL1Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\LL1Parser\LookupFirst;

class LookupFirstTest extends TestCase
{

    public function testGet_Constructed_ReturnsEmptyArray(): void
    {
        $expectedValue = [];
        $actualValue = (new LookupFirst)->get(1);
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @param array $tokenIdList
     * @dataProvider providerAddTokenCalledOnce
     */
    public function testAddToken_CalledOnce_GetReturnsAddedTokens(array $tokenIdList): void
    {
        $lookupFirst = new LookupFirst;
        $lookupFirst->addToken(1, ...$tokenIdList);
        $actualValue = $lookupFirst->get(1);
        sort($actualValue);
        self::assertSame($tokenIdList, $actualValue);
    }

    public function providerAddTokenCalledOnce(): array
    {
        return [
            "No tokens" => [[]],
            "One token" => [[2]],
            "Two tokens" => [[2, 3]],
        ];
    }

    /**
     * @param array $firstTokenIdList
     * @param array $secondTokenIdList
     * @param array $expectedValue
     * @dataProvider providerAddTokenCalledTwice
     */
    public function testAddToken_CalledTwice_GetReturnsMergedTokens(
        array $firstTokenIdList,
        array $secondTokenIdList,
        array $expectedValue
    ): void {
        $lookupFirst = new LookupFirst;
        $lookupFirst->addToken(1, ...$firstTokenIdList);
        $lookupFirst->addToken(1, ...$secondTokenIdList);
        $actualValue = $lookupFirst->get(1);
        sort($actualValue);
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @param array $firstTokenIdList
     * @param array $secondTokenIdList
     * @param array $mergedList
     * @dataProvider providerAddTokenCalledTwice
     */
    public function testAddToken_CalledTwice_GetChangeCountReturnsMergedTokensAmount(
        array $firstTokenIdList,
        array $secondTokenIdList,
        array $mergedList
    ): void {
        $lookupFirst = new LookupFirst;
        $lookupFirst->addToken(1, ...$firstTokenIdList);
        $lookupFirst->addToken(1, ...$secondTokenIdList);
        $expectedValue = count($mergedList);
        $actualValue = $lookupFirst->getChangeCount();
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerAddTokenCalledTwice(): array
    {
        return [
            "Non-crossing sets" => [[2, 3], [4], [2, 3, 4]],
            "Partially crossing sets" => [[2, 3], [3, 4], [2, 3, 4]],
            "Fully crossing sets" => [[2, 3], [3, 2], [2, 3]],
            "Empty second set" => [[2, 3], [], [2, 3]],
            "Two empty sets" => [[], [], []],
        ];
    }

    public function testHasEpsilon_NoEpsilonsAdded_ReturnsFalse(): void
    {
        $actualValue = (new LookupFirst)->hasEpsilon(1);
        self::assertFalse($actualValue);
    }

    public function testHasEpsilon_EmptyTerminalList_ReturnsTrue(): void
    {
        $actualValue = (new LookupFirst)->hasEpsilon();
        self::assertTrue($actualValue);
    }

    public function testAddEpsilon_Constructed_HasEpsilonReturnsTrue(): void
    {
        $lookupFirst = new LookupFirst;
        $lookupFirst->addEpsilon(1);
        $actualValue = $lookupFirst->hasEpsilon(1);
        self::assertTrue($actualValue);
    }

    public function testAddEpsilon_CalledOnce_CounterTriggeredOnce(): void
    {
        $lookupFirst = new LookupFirst;
        $lookupFirst->addEpsilon(1);
        $actualValue = $lookupFirst->getChangeCount();
        self::assertSame(1, $actualValue);
    }

    public function testAddEpsilon_CalledTwiceForSameProduction_CounterTriggeredOnce(): void
    {
        $lookupFirst = new LookupFirst;
        $lookupFirst->addEpsilon(1);
        $lookupFirst->addEpsilon(1);
        $actualValue = $lookupFirst->getChangeCount();
        self::assertSame(1, $actualValue);
    }

    public function testAddEpsilon_CalledTwiceForDifferentProductions_CounterTriggeredTwice(): void
    {
        $lookupFirst = new LookupFirst;
        $lookupFirst->addEpsilon(1);
        $lookupFirst->addEpsilon(2);
        $actualValue = $lookupFirst->getChangeCount();
        self::assertSame(2, $actualValue);
    }

    public function testGetChangeCount_Constructed_ReturnsZero(): void
    {
        $actualValue = (new LookupFirst)->getChangeCount();
        self::assertSame(0, $actualValue);
    }

    public function testResetChangeCount_CounterTriggered_GetChangeCountReturnsZero(): void
    {
        $lookupFirst = new LookupFirst;
        $lookupFirst->addEpsilon(1);
        $lookupFirst->resetChangeCount();
        $actualValue = $lookupFirst->getChangeCount();
        self::assertSame(0, $actualValue);
    }

    /**
     * @dataProvider providerMergeTokens
     * @param int $sourceProductionId
     * @param array $sourceTokenIdList
     * @param int $targetProductionId
     * @param array $targetTokenIdList
     * @param array $expectedValue
     */
    public function testMergeTokens_TokensSet_TargetGetReturnsMergedTokens(
        int $sourceProductionId,
        array $sourceTokenIdList,
        int $targetProductionId,
        array $targetTokenIdList,
        array $expectedValue
    ): void {
        $lookupFirst = new LookupFirst;
        $lookupFirst->addToken($sourceProductionId, ...$sourceTokenIdList);
        $lookupFirst->addToken($targetProductionId, ...$targetTokenIdList);
        $lookupFirst->mergeTokens($targetProductionId, $sourceProductionId);
        $actualValue = $lookupFirst->get($targetProductionId);
        sort($actualValue);
        self::assertSame($expectedValue, $actualValue);
    }

    public function providerMergeTokens(): array
    {
        return [
            "Both sets are empty" => [1, [], 2, [], []],
            "Both sets are non-empty" => [1, [2, 4], 3, [4, 5], [2, 4, 5]],
            "Source set is empty" => [1, [], 2, [3, 4], [3, 4]],
            "Target set is empty" => [1, [3, 2], 4, [], [2, 3]],
            "Target same as source" => [1, [2, 3], 1, [3, 4], [2, 3, 4]],
        ];
    }

    public function testMergeEpsilons_AllMergedNonTerminalsHaveEpsilons_HasEpsilonReturnsFalse(): void
    {
        $lookupFirst = new LookupFirst;
        $lookupFirst->addEpsilon(1);
        $lookupFirst->addEpsilon(2);
        $lookupFirst->mergeEpsilons(3, 1, 2);
        self::assertTrue($lookupFirst->hasEpsilon(3));
    }

    public function testMergeEpsilons_NotAllMergedNonTerminalsHaveEpsilons_HasEpsilonReturnsFalse(): void
    {
        $lookupFirst = new LookupFirst;
        $lookupFirst->addEpsilon(1);
        $lookupFirst->mergeEpsilons(3, 1, 2);
        self::assertFalse($lookupFirst->hasEpsilon(3));
    }
}
