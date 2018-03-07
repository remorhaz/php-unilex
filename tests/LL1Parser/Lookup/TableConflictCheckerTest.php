<?php

namespace Remorhaz\UniLex\Test\LL1Parser\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\LL1Parser\Lookup\FirstBuilder;
use Remorhaz\UniLex\LL1Parser\Lookup\FollowBuilder;
use Remorhaz\UniLex\LL1Parser\Lookup\TableConflictChecker;

/**
 * @covers \Remorhaz\UniLex\LL1Parser\Lookup\TableConflictChecker
 */
class TableConflictCheckerTest extends TestCase
{

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage FIRST(1:0)/FIRST(1:1) conflict: 5
     */
    public function testCheck_GrammarHasFirstFirstConflict_ThrowsException(): void
    {
        $config = [
            GrammarLoader::TOKEN_MAP_KEY => [2 => 5, 3 => 5, 4 => 1],
            GrammarLoader::PRODUCTION_MAP_KEY => [1 => [[2], [3]]],
            GrammarLoader::START_SYMBOL_KEY => 1,
            GrammarLoader::EOI_SYMBOL_KEY => 4,
        ];
        $grammar = GrammarLoader::loadConfig($config);
        $first = (new FirstBuilder($grammar))->getFirst();
        $follow = (new FollowBuilder($grammar, $first))->getFollow();
        (new TableConflictChecker($grammar, $first, $follow))->check();
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage FIRST(2:0)/FOLLOW(2) conflict (ε ∈ 2:1): 1
     */
    public function testCheck_GrammarHasFirstFollowConflict_ThrowsException(): void
    {
        $config = [
            GrammarLoader::TOKEN_MAP_KEY => [3 => 1, 4 => 2],
            GrammarLoader::PRODUCTION_MAP_KEY => [1 => [[2, 3]], 2 => [[1], []]],
            GrammarLoader::START_SYMBOL_KEY => 1,
            GrammarLoader::EOI_SYMBOL_KEY => 4,
        ];
        $grammar = GrammarLoader::loadConfig($config);
        $first = (new FirstBuilder($grammar))->getFirst();
        $follow = (new FollowBuilder($grammar, $first))->getFollow();
        (new TableConflictChecker($grammar, $first, $follow))->check();
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Symbol 1 has multiple ε-productions
     */
    public function testCheck_GrammarHasProductionWithMultipleEpsilons_ThrowsException(): void
    {
        $config = [
            GrammarLoader::TOKEN_MAP_KEY => [2 => 1, 3 => 2],
            GrammarLoader::PRODUCTION_MAP_KEY => [1 => [[2], [], []]],
            GrammarLoader::START_SYMBOL_KEY => 1,
            GrammarLoader::EOI_SYMBOL_KEY => 3,
        ];
        $grammar = GrammarLoader::loadConfig($config);
        $first = (new FirstBuilder($grammar))->getFirst();
        $follow = (new FollowBuilder($grammar, $first))->getFollow();
        (new TableConflictChecker($grammar, $first, $follow))->check();
    }
}
