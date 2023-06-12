<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Parser\LL1\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\SymbolType;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Exception as UnilexException;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Parser\LL1\Lookup\FirstBuilder;
use Remorhaz\UniLex\Parser\LL1\Lookup\FollowBuilder;

/**
 * @covers \Remorhaz\UniLex\Parser\LL1\Lookup\FollowBuilder
 */
class FollowBuilderTest extends TestCase
{
    /**
     * @param string $configFile
     * @param int $symbolId
     * @param list<int> $expectedValue
     * @throws UnilexException
     * @dataProvider providerValidGrammars
     */
    public function testGetFollow_ValidGrammar_ResultGetReturnsMatchingValue(
        string $configFile,
        int $symbolId,
        array $expectedValue
    ): void {
        $grammar = GrammarLoader::loadFile($configFile);
        $first = (new FirstBuilder($grammar))->getFirst();
        $follow = (new FollowBuilder($grammar, $first))->getFollow();
        $actualValue = $follow->getTokens($symbolId);
        sort($actualValue);
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @return iterable<string, array{string, int, list<int>}>
     */
    public static function providerValidGrammars(): iterable
    {
        $data = [];
        foreach (self::getSimpleExprGrammarFollowList() as $key => $follows) {
            [$symbolId, $expectedFollows] = $follows;
            $data["Grammar from SimpleExpr example, symbol $key"] =
                [ConfigFile::getPath(), $symbolId, $expectedFollows];
        }
        return $data;
    }

    /**
     * @return iterable<string, array{int, list<int>}>
     */
    private static function getSimpleExprGrammarFollowList(): iterable
    {
        return [
            "NT_E0" => [SymbolType::NT_E0, [TokenType::R_PARENTHESIS, TokenType::EOI]],
            "NT_E1" => [SymbolType::NT_E1, [TokenType::R_PARENTHESIS, TokenType::EOI]],
            "NT_T0" => [SymbolType::NT_T0, [TokenType::PLUS, TokenType::R_PARENTHESIS, TokenType::EOI]],
            "NT_T1" => [SymbolType::NT_T1, [TokenType::PLUS, TokenType::R_PARENTHESIS, TokenType::EOI]],
            "NT_F"  => [
                SymbolType::NT_F,
                [
                    TokenType::PLUS,
                    TokenType::STAR,
                    TokenType::R_PARENTHESIS,
                    TokenType::EOI,
                ],
            ],
        ];
    }
}
