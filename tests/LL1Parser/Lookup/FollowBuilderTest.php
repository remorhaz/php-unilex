<?php

namespace Remorhaz\UniLex\Test\LL1Parser\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ProductionType;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Grammar\ContextFreeGrammarLoader;
use Remorhaz\UniLex\LL1Parser\Lookup\FirstBuilder;
use Remorhaz\UniLex\LL1Parser\Lookup\FollowBuilder;

/**
 * @covers \Remorhaz\UniLex\LL1Parser\Lookup\FollowBuilder
 */
class FollowBuilderTest extends TestCase
{

    /**
     * @param string $configFile
     * @param int $symbolId
     * @param array $expectedValue
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidGrammars
     */
    public function testGetFollow_ValidGrammar_ResultGetReturnsMatchingValue(
        string $configFile,
        int $symbolId,
        array $expectedValue
    ): void {
        $grammar = ContextFreeGrammarLoader::loadFile($configFile);
        $first = (new FirstBuilder($grammar))->getFirst();
        $follow = (new FollowBuilder($grammar, $first))->getFollow();
        $actualValue = $follow->getTokens($symbolId);
        sort($actualValue);
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerValidGrammars(): array
    {
        $data = [];
        foreach ($this->getSimpleExprGrammarFollowList() as $key => $follows) {
            [$symbolId, $expectedFollows] = $follows;
            $data["Grammar from SimpleExpr example, symbol {$key}"] =
                [ConfigFile::getPath(), $symbolId, $expectedFollows];
        }
        return $data;
    }

    private function getSimpleExprGrammarFollowList(): array
    {
        return [
            "NT_E0" => [ProductionType::NT_E0, [TokenType::R_PARENTHESIS, TokenType::EOI]],
            "NT_E1" => [ProductionType::NT_E1, [TokenType::R_PARENTHESIS, TokenType::EOI]],
            "NT_T0" => [ProductionType::NT_T0, [TokenType::PLUS, TokenType::R_PARENTHESIS, TokenType::EOI]],
            "NT_T1" => [ProductionType::NT_T1, [TokenType::PLUS, TokenType::R_PARENTHESIS, TokenType::EOI]],
            "NT_F"  => [
                ProductionType::NT_F,
                [
                    TokenType::PLUS,
                    TokenType::STAR,
                    TokenType::R_PARENTHESIS,
                    TokenType::EOI,
                ]
            ],
        ];
    }
}