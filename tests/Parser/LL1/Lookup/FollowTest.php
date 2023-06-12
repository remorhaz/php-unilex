<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Parser\LL1\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Parser\LL1\Lookup\Follow;

/**
 * @covers \Remorhaz\UniLex\Parser\LL1\Lookup\Follow
 * @covers \Remorhaz\UniLex\Parser\LL1\Lookup\Set
 */
class FollowTest extends TestCase
{
    public function testGetTokens_Constructed_ReturnsEmptyArray(): void
    {
        $actualValue = (new Follow())->getTokens(1);
        self::assertSame([], $actualValue);
    }

    /**
     * @param list<int> $tokenIdList
     * @dataProvider providerAddTokenCalledOnce
     */
    public function testAddToken_CalledOnce_GetTokenReturnsAddedTokens(array $tokenIdList): void
    {
        $lookupFirst = new Follow();
        $lookupFirst->addToken(1, ...$tokenIdList);
        $actualValue = $lookupFirst->getTokens(1);
        sort($actualValue);
        self::assertSame($tokenIdList, $actualValue);
    }

    /**
     * @return iterable<string, array{list<int>}>
     */
    public static function providerAddTokenCalledOnce(): iterable
    {
        return [
            "No tokens" => [[]],
            "One token" => [[2]],
            "Two tokens" => [[2, 3]],
        ];
    }

    /**
     * @param list<int> $firstTokenIdList
     * @param list<int> $secondTokenIdList
     * @param list<int> $expectedValue
     * @dataProvider providerAddTokenCalledTwice
     */
    public function testAddToken_CalledTwice_GetTokenReturnsMergedTokens(
        array $firstTokenIdList,
        array $secondTokenIdList,
        array $expectedValue
    ): void {
        $lookupFirst = new Follow();
        $lookupFirst->addToken(1, ...$firstTokenIdList);
        $lookupFirst->addToken(1, ...$secondTokenIdList);
        $actualValue = $lookupFirst->getTokens(1);
        sort($actualValue);
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @param list<int> $firstTokenIdList
     * @param list<int> $secondTokenIdList
     * @param list<int> $mergedList
     * @dataProvider providerAddTokenCalledTwice
     */
    public function testAddToken_CalledTwice_GetChangeCountReturnsMergedTokensAmount(
        array $firstTokenIdList,
        array $secondTokenIdList,
        array $mergedList
    ): void {
        $lookupFirst = new Follow();
        $lookupFirst->addToken(1, ...$firstTokenIdList);
        $lookupFirst->addToken(1, ...$secondTokenIdList);
        $expectedValue = count($mergedList);
        $actualValue = $lookupFirst->getChangeCount();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @return iterable<string, array{list<int>, list<int>, list<int>}>
     */
    public static function providerAddTokenCalledTwice(): iterable
    {
        return [
            "Non-crossing sets" => [[2, 3], [4], [2, 3, 4]],
            "Partially crossing sets" => [[2, 3], [3, 4], [2, 3, 4]],
            "Fully crossing sets" => [[2, 3], [3, 2], [2, 3]],
            "Empty second set" => [[2, 3], [], [2, 3]],
            "Two empty sets" => [[], [], []],
        ];
    }

    public function testGetChangeCount_Constructed_ReturnsZero(): void
    {
        $actualValue = (new Follow())->getChangeCount();
        self::assertSame(0, $actualValue);
    }

    public function testResetChangeCount_CounterTriggered_GetChangeCountReturnsZero(): void
    {
        $lookupFirst = new Follow();
        $lookupFirst->addToken(1, 2);
        $lookupFirst->resetChangeCount();
        $actualValue = $lookupFirst->getChangeCount();
        self::assertSame(0, $actualValue);
    }

    /**
     * @dataProvider providerMergeTokens
     * @param int $sourceProductionId
     * @param list<int> $sourceTokenIdList
     * @param int $targetProductionId
     * @param list<int> $targetTokenIdList
     * @param list<int> $expectedValue
     */
    public function testMergeTokens_TokensSet_TargetGetTokensReturnsMergedTokens(
        int $sourceProductionId,
        array $sourceTokenIdList,
        int $targetProductionId,
        array $targetTokenIdList,
        array $expectedValue
    ): void {
        $lookupFirst = new Follow();
        $lookupFirst->addToken($sourceProductionId, ...$sourceTokenIdList);
        $lookupFirst->addToken($targetProductionId, ...$targetTokenIdList);
        $lookupFirst->mergeTokens($targetProductionId, $sourceProductionId);
        $actualValue = $lookupFirst->getTokens($targetProductionId);
        sort($actualValue);
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @return iterable<string, array{int, list<int>, int, list<int>, list<int>}>
     */
    public static function providerMergeTokens(): iterable
    {
        return [
            "Both sets are empty" => [1, [], 2, [], []],
            "Both sets are non-empty" => [1, [2, 4], 3, [4, 5], [2, 4, 5]],
            "Source set is empty" => [1, [], 2, [3, 4], [3, 4]],
            "Target set is empty" => [1, [3, 2], 4, [], [2, 3]],
            "Target same as source" => [1, [2, 3], 1, [3, 4], [2, 3, 4]],
        ];
    }
}
