<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Parser\LL1\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Parser\LL1\Lookup\First;

/**
 * @covers \Remorhaz\UniLex\Parser\LL1\Lookup\First
 */
class FirstTest extends TestCase
{
    public function testGetProductionTokens_Constructed_ReturnsEmptyArray(): void
    {
        $expectedValue = [];
        $actualValue = (new First())->getProductionTokens(1, 2);
        self::assertSame($expectedValue, $actualValue);
    }

    public function testHasEpsilon_NoEpsilonsAdded_ReturnsFalse(): void
    {
        $actualValue = (new First())->hasEpsilon(1);
        self::assertFalse($actualValue);
    }

    public function testProductionHasEpsilon_EmptyTerminalList_ReturnsTrue(): void
    {
        $actualValue = (new First())->productionHasEpsilon();
        self::assertTrue($actualValue);
    }

    public function testAddEpsilon_Constructed_HasEpsilonReturnsTrue(): void
    {
        $lookupFirst = new First();
        $lookupFirst->addEpsilon(1);
        $actualValue = $lookupFirst->hasEpsilon(1);
        self::assertTrue($actualValue);
    }

    public function testAddEpsilon_CalledOnce_CounterTriggeredOnce(): void
    {
        $lookupFirst = new First();
        $lookupFirst->addEpsilon(1);
        $actualValue = $lookupFirst->getChangeCount();
        self::assertSame(1, $actualValue);
    }

    public function testAddEpsilon_CalledTwiceForSameProduction_CounterTriggeredOnce(): void
    {
        $lookupFirst = new First();
        $lookupFirst->addEpsilon(1);
        $lookupFirst->addEpsilon(1);
        $actualValue = $lookupFirst->getChangeCount();
        self::assertSame(1, $actualValue);
    }

    public function testAddEpsilon_CalledTwiceForDifferentProductions_CounterTriggeredTwice(): void
    {
        $lookupFirst = new First();
        $lookupFirst->addEpsilon(1);
        $lookupFirst->addEpsilon(2);
        $actualValue = $lookupFirst->getChangeCount();
        self::assertSame(2, $actualValue);
    }

    /**
     * @dataProvider providerMergeProductionTokens
     * @param int $sourceProductionId
     * @param list<int> $sourceTokenIdList
     * @param int $targetProductionId
     * @param list<int> $targetTokenIdList
     * @param list<int> $expectedValue
     */
    public function testMergeProductionTokens_TokensSet_TargetGetTokensReturnsMergedTokens(
        int $sourceProductionId,
        array $sourceTokenIdList,
        int $targetProductionId,
        array $targetTokenIdList,
        array $expectedValue
    ): void {
        $lookupFirst = new First();
        $lookupFirst->addToken($sourceProductionId, ...$sourceTokenIdList);
        $lookupFirst->addToken($targetProductionId, ...$targetTokenIdList);
        $lookupFirst->mergeProductionTokens($targetProductionId, $sourceProductionId);
        $actualValue = $lookupFirst->getTokens($targetProductionId);
        sort($actualValue);
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @return iterable<string, array{int, list<int>, int, list<int>, list<int>}>
     */
    public static function providerMergeProductionTokens(): iterable
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
        $lookupFirst = new First();
        $lookupFirst->addEpsilon(1);
        $lookupFirst->addEpsilon(2);
        $lookupFirst->mergeProductionEpsilons(3, 1, 2);
        self::assertTrue($lookupFirst->productionHasEpsilon(3));
    }

    public function testMergeEpsilons_NotAllMergedNonTerminalsHaveEpsilons_HasEpsilonReturnsFalse(): void
    {
        $lookupFirst = new First();
        $lookupFirst->addEpsilon(1);
        $lookupFirst->mergeProductionEpsilons(3, 1, 2);
        self::assertFalse($lookupFirst->productionHasEpsilon(3));
    }
}
