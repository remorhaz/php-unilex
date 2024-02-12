<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Parser\LL1\Lookup;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\SymbolType;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Parser\LL1\Lookup\TableBuilder;
use Remorhaz\UniLex\Parser\LL1\Lookup\TableConflictChecker;

#[
    CoversClass(TableBuilder::class),
    CoversClass(TableConflictChecker::class),
]
class TableBuilderTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[DataProvider('providerValidGrammarTables')]
    public function testGetFirst_ValidGrammar_ResultGetTokensReturnsMatchingValue(
        string $configFile,
        array $expectedValue,
    ): void {
        $grammar = GrammarLoader::loadFile($configFile);
        $actualValue = (new TableBuilder($grammar))->getTable()->exportMap();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @return iterable<string, array{string, array<int, array<int, int>>}>
     */
    public static function providerValidGrammarTables(): iterable
    {
        return [
            "Grammar from SimpleExpr example" => [
                ConfigFile::getPath(),
                self::getSimpleExprGrammarTable(),
            ],
        ];
    }

    /**
     * @return array<int, array<int, int>>
     */
    private static function getSimpleExprGrammarTable(): array
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
