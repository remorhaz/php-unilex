<?php

namespace Remorhaz\UniLex\Test\LL1Parser\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Parser\LL1\Lookup\Follow;

/**
 * @covers \Remorhaz\UniLex\Parser\LL1\Lookup\Follow<extended>
 */
class FollowTest extends TestCase
{

    public function testGetTokens_Constructed_ReturnsEmptyArray(): void
    {
        $actualValue = (new Follow)->getTokens(1);
        self::assertSame([], $actualValue);
    }

    /**
     * @param array $tokenIdList
     * @dataProvider providerAddTokenCalledOnce
     */
    public function testAddToken_CalledOnce_GetTokenReturnsAddedTokens(array $tokenIdList): void
    {
        $lookupFirst = new Follow;
        $lookupFirst->addToken(1, ...$tokenIdList);
        $actualValue = $lookupFirst->getTokens(1);
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
        $lookupFirst = new Follow;
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

    public function testGetChangeCount_Constructed_ReturnsZero(): void
    {
        $actualValue = (new Follow)->getChangeCount();
        self::assertSame(0, $actualValue);
    }

    public function testResetChangeCount_CounterTriggered_GetChangeCountReturnsZero(): void
    {
        $lookupFirst = new Follow;
        $lookupFirst->addToken(1, 2);
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
    public function testMergeTokens_TokensSet_TargetGetTokensReturnsMergedTokens(
        int $sourceProductionId,
        array $sourceTokenIdList,
        int $targetProductionId,
        array $targetTokenIdList,
        array $expectedValue
    ): void {
        $lookupFirst = new Follow;
        $lookupFirst->addToken($sourceProductionId, ...$sourceTokenIdList);
        $lookupFirst->addToken($targetProductionId, ...$targetTokenIdList);
        $lookupFirst->mergeTokens($targetProductionId, $sourceProductionId);
        $actualValue = $lookupFirst->getTokens($targetProductionId);
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
}
