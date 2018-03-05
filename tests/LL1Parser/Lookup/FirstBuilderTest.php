<?php

namespace Remorhaz\UniLex\Test\LL1Parser\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\SymbolType;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\LL1Parser\Lookup\FirstBuilder;

/**
 * @covers \Remorhaz\UniLex\LL1Parser\Lookup\FirstBuilder
 */
class FirstBuilderTest extends TestCase
{

    /**
     * @param string $configFile
     * @param int $symbolId
     * @param array $expectedValue
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidGrammarFirsts
     */
    public function testGetFirst_ValidGrammar_ResultGetTokensReturnsMatchingValue(
        string $configFile,
        int $symbolId,
        array $expectedValue
    ): void {
        $grammar = GrammarLoader::loadFile($configFile);
        $first = (new FirstBuilder($grammar))->getFirst();
        $actualValue = $first->getTokens($symbolId);
        sort($actualValue);
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerValidGrammarFirsts(): array
    {
        $data = [];
        foreach ($this->getSimpleExprGrammarFirstList() as $key => $firsts) {
            [$symbolId, $expectedFirst] = $firsts;
            $data["Grammar from SimpleExpr example, symbol {$key}"] =
                [ConfigFile::getPath(), $symbolId, $expectedFirst];
        }
        return $data;
    }

    private function getSimpleExprGrammarFirstList(): array
    {
        return [
            "T_PLUS"            => [SymbolType::T_PLUS, [TokenType::PLUS]],
            "T_STAR"            => [SymbolType::T_STAR, [TokenType::STAR]],
            "T_L_PARENTHESIS"   => [SymbolType::T_L_PARENTHESIS, [TokenType::L_PARENTHESIS]],
            "T_R_PARENTHESIS"   => [SymbolType::T_R_PARENTHESIS, [TokenType::R_PARENTHESIS]],
            "T_ID"              => [SymbolType::T_ID, [TokenType::ID]],
            "T_EOI"             => [SymbolType::T_EOI, [TokenType::EOI]],
            "NT_E0"             => [SymbolType::NT_E0, [TokenType::L_PARENTHESIS, TokenType::ID]],
            "NT_E1"             => [SymbolType::NT_E1, [TokenType::PLUS]],
            "NT_T0"             => [SymbolType::NT_T0, [TokenType::L_PARENTHESIS, TokenType::ID]],
            "NT_T1"             => [SymbolType::NT_T1, [TokenType::STAR]],
            "NT_F"              => [SymbolType::NT_F, [TokenType::L_PARENTHESIS, TokenType::ID]],
        ];
    }

    /**
     * @param string $configFile
     * @param int $symbolId
     * @param bool $expectedValue
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidGrammarEpsilons
     */
    public function testGetFirst_ValidGrammar_ResultHasEpsilonReturnsMatchingValue(
        string $configFile,
        int $symbolId,
        bool $expectedValue
    ): void {
        $grammar = GrammarLoader::loadFile($configFile);
        $first = (new FirstBuilder($grammar))->getFirst();
        $actualValue = $first->productionHasEpsilon($symbolId);
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerValidGrammarEpsilons(): array
    {
        $data = [];
        foreach ($this->getSimpleExprGrammarEpsilonList() as $key => $epsilon) {
            [$symbolId, $expectedValue] = $epsilon;
            $data["Grammar from SimpleExpr example, symbol {$key}"] =
                [ConfigFile::getPath(), $symbolId, $expectedValue];
        }
        return $data;
    }

    private function getSimpleExprGrammarEpsilonList(): array
    {
        return [
            "T_PLUS"            => [SymbolType::T_PLUS, false],
            "T_STAR"            => [SymbolType::T_STAR, false],
            "T_L_PARENTHESIS"   => [SymbolType::T_L_PARENTHESIS, false],
            "T_R_PARENTHESIS"   => [SymbolType::T_R_PARENTHESIS, false],
            "T_ID"              => [SymbolType::T_ID, false],
            "T_EOI"             => [SymbolType::T_EOI, false],
            "NT_E0"             => [SymbolType::NT_E0, false],
            "NT_E1"             => [SymbolType::NT_E1, true],
            "NT_T0"             => [SymbolType::NT_T0, false],
            "NT_T1"             => [SymbolType::NT_T1, true],
            "NT_F"              => [SymbolType::NT_F, false],
        ];
    }
}
