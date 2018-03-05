<?php

namespace Remorhaz\UniLex\Test\LL1Parser\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\SymbolType;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\LL1Parser\Lookup\TableBuilder;

/**
 * @covers \Remorhaz\UniLex\LL1Parser\Lookup\TableBuilder
 */
class TableBuilderTest extends TestCase
{

    /**
     * @param string $configFile
     * @param array $expectedValue
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidGrammarTables
     */
    public function testGetFirst_ValidGrammar_ResultGetTokensReturnsMatchingValue(
        string $configFile,
        array $expectedValue
    ): void {
        $grammar = GrammarLoader::loadFile($configFile);
        $actualValue = (new TableBuilder($grammar))->getTable()->exportMap();
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerValidGrammarTables(): array
    {
        return [
            "Grammar from SimpleExpr example" => [
                ConfigFile::getPath(),
                $this->getSimpleExprGrammarTable(),
            ],
        ];
    }

    private function getSimpleExprGrammarTable(): array
    {
        return [
            SymbolType::NT_E0 => [
                TokenType::L_PARENTHESIS => 0,
                TokenType::ID => 0,
            ],
            SymbolType::NT_E1 => [
                TokenType::PLUS => 0,
                TokenType::R_PARENTHESIS => 1,
                TokenType::EOI => 1,
            ],
            SymbolType::NT_T0 => [
                TokenType::L_PARENTHESIS => 0,
                TokenType::ID => 0,
            ],
            SymbolType::NT_T1 => [
                TokenType::PLUS => 1,
                TokenType::STAR => 0,
                TokenType::R_PARENTHESIS => 1,
                TokenType::EOI => 1,
            ],
            SymbolType::NT_F => [
                TokenType::L_PARENTHESIS => 0,
                TokenType::ID => 1,
            ],
        ];
    }
}
