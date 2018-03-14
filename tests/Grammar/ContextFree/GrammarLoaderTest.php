<?php

namespace Remorhaz\UniLex\Test\Grammar\ContextFree;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;

/**
 * @covers \Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader
 */
class GrammarLoaderTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testLoadConfig_ValidConfig_GrammarHasMatchingTerminalList(): void
    {
        $config = [
            GrammarLoader::TOKEN_MAP_KEY => [1 => 1, 2 => 2],
            GrammarLoader::PRODUCTION_MAP_KEY => [3 => [[1, 4]], 4 => [[1], []]],
            GrammarLoader::ROOT_SYMBOL_KEY => 0,
            GrammarLoader::START_SYMBOL_KEY => 3,
            GrammarLoader::EOI_SYMBOL_KEY => 2,
        ];
        $expectedValue = [1, 2];
        $actualValue = GrammarLoader::loadConfig($config)->getTerminalList();
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testLoadConfig_ValidConfig_GrammarHasMatchingToken(): void
    {
        $config = [
            GrammarLoader::TOKEN_MAP_KEY => [1 => 1, 2 => 2],
            GrammarLoader::PRODUCTION_MAP_KEY => [3 => [[1, 4]], 4 => [[1], []]],
            GrammarLoader::ROOT_SYMBOL_KEY => 0,
            GrammarLoader::START_SYMBOL_KEY => 3,
            GrammarLoader::EOI_SYMBOL_KEY => 2,
        ];
        $actualValue = GrammarLoader::loadConfig($config)->getToken(2);
        self::assertSame(2, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testLoadConfig_ValidConfig_GrammarHasMatchingNonTerminalList(): void
    {
        $config = [
            GrammarLoader::TOKEN_MAP_KEY => [1 => 1, 2 => 2],
            GrammarLoader::PRODUCTION_MAP_KEY => [3 => [[1, 4]], 4 => [[1], []]],
            GrammarLoader::ROOT_SYMBOL_KEY => 0,
            GrammarLoader::START_SYMBOL_KEY => 3,
            GrammarLoader::EOI_SYMBOL_KEY => 2,
        ];
        $expectedValue = [3, 4];
        $actualValue = GrammarLoader::loadConfig($config)->getNonTerminalList();
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testLoadConfig_ValidConfig_GrammarHasMatchingProduction(): void
    {
        $config = [
            GrammarLoader::TOKEN_MAP_KEY => [1 => 1, 2 => 2],
            GrammarLoader::PRODUCTION_MAP_KEY => [3 => [[1, 4]], 4 => [[1], []]],
            GrammarLoader::ROOT_SYMBOL_KEY => 0,
            GrammarLoader::START_SYMBOL_KEY => 3,
            GrammarLoader::EOI_SYMBOL_KEY => 2,
        ];
        $expectedValue = [1, 4];
        $actualValue = GrammarLoader::loadConfig($config)
            ->getProduction(3, 0)
            ->getSymbolList();
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testLoadConfig_ValidConfig_GrammarHasMatchingStartSymbol(): void
    {
        $config = [
            GrammarLoader::TOKEN_MAP_KEY => [1 => 1, 2 => 2],
            GrammarLoader::PRODUCTION_MAP_KEY => [3 => [[1, 4]], 4 => [[1], []]],
            GrammarLoader::ROOT_SYMBOL_KEY => 0,
            GrammarLoader::START_SYMBOL_KEY => 3,
            GrammarLoader::EOI_SYMBOL_KEY => 2,
        ];
        $actualValue = GrammarLoader::loadConfig($config)->getStartSymbol();
        self::assertSame(3, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testLoadConfig_ValidConfig_GrammarHasMatchingEoiToken(): void
    {
        $config = [
            GrammarLoader::TOKEN_MAP_KEY => [1 => 1, 2 => 2],
            GrammarLoader::PRODUCTION_MAP_KEY => [3 => [[1, 4]], 4 => [[1], []]],
            GrammarLoader::ROOT_SYMBOL_KEY => 0,
            GrammarLoader::START_SYMBOL_KEY => 3,
            GrammarLoader::EOI_SYMBOL_KEY => 2,
        ];
        $actualValue = GrammarLoader::loadConfig($config)->getEoiToken();
        self::assertSame(2, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Key 'start_symbol' not found in config
     */
    public function testLoadConfig_NoStartSymbolKey_ThrowsException(): void
    {
        $config = [
            GrammarLoader::TOKEN_MAP_KEY => [1 => 1, 2 => 2],
            GrammarLoader::PRODUCTION_MAP_KEY => [3 => [[1, 4]], 4 => [[1], []]],
            GrammarLoader::ROOT_SYMBOL_KEY => 0,
            GrammarLoader::EOI_SYMBOL_KEY => 2,
        ];
        GrammarLoader::loadConfig($config);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Config should be an array
     */
    public function testLoadConfig_NotArray_ThrowsException(): void
    {
        /** @var array $config */
        $config = (object) ["a" => "b"];
        GrammarLoader::loadConfig($config);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testLoadFile_ValidFile_GrammarHasMatchingEoiToken(): void
    {
        $actualValue = GrammarLoader::loadFile(ConfigFile::getPath())->getEoiToken();
        self::assertSame(TokenType::EOI, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessageRegExp #^Config file .+NotExisting\.php not found$#
     */
    public function testLoadFile_FileNotExists_ThrowsException(): void
    {
        GrammarLoader::loadFile(__DIR__ . "/NotExisting.php");
    }
}
