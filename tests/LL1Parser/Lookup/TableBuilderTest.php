<?php

namespace Remorhaz\UniLex\Test\LL1Parser\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ProductionType;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Grammar\ContextFreeGrammarLoader;
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
        $grammar = ContextFreeGrammarLoader::loadFile($configFile);
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
            ProductionType::NT_E0 => [
                TokenType::L_PARENTHESIS => [ProductionType::NT_T0, ProductionType::NT_E1],
                TokenType::ID => [ProductionType::NT_T0, ProductionType::NT_E1],
            ],
            ProductionType::NT_E1 => [
                TokenType::PLUS => [ProductionType::T_PLUS, ProductionType::NT_T0, ProductionType::NT_E1],
                TokenType::R_PARENTHESIS => [],
                TokenType::EOI => [],
            ],
            ProductionType::NT_T0 => [
                TokenType::L_PARENTHESIS => [ProductionType::NT_F, ProductionType::NT_T1],
                TokenType::ID => [ProductionType::NT_F, ProductionType::NT_T1],
            ],
            ProductionType::NT_T1 => [
                TokenType::PLUS => [],
                TokenType::STAR => [ProductionType::T_STAR, ProductionType::NT_F, ProductionType::NT_T1],
                TokenType::R_PARENTHESIS => [],
                TokenType::EOI => [],
            ],
            ProductionType::NT_F => [
                TokenType::L_PARENTHESIS => [
                    ProductionType::T_L_PARENTHESIS,
                    ProductionType::NT_E0,
                    ProductionType::T_R_PARENTHESIS,
                ],
                TokenType::ID => [ProductionType::T_ID],
            ],
        ];
    }
}
