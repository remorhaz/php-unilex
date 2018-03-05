<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\LexemeFactory;
use Remorhaz\UniLex\LexemeReader;
use Remorhaz\UniLex\LL1Parser\Parser;
use Remorhaz\UniLex\RegExp\Grammar\ConfigFile;
use Remorhaz\UniLex\RegExp\Grammar\ProductionType;
use Remorhaz\UniLex\RegExp\Grammar\TokenType;
use Remorhaz\UniLex\RegExp\LexemeMatcher;
use Remorhaz\UniLex\LL1Parser\ParseTreeBuilder;
use Remorhaz\UniLex\Unicode\BufferFactory;

/**
 * @coversNothing
 */
class ParserTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @todo Experimental test
     */
    public function testParser()
    {
        $buffer = BufferFactory::createFromUtf8String('hello');
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $reader = new LexemeReader($buffer, new LexemeMatcher, new LexemeFactory($grammar));
        $listener = new ParseTreeBuilder($grammar);
        $parser = new Parser($grammar, $reader, $listener);
        $parser->run();
        $expectedLexemeTypeLog = [
            TokenType::OTHER_ASCII_LETTER,
            TokenType::OTHER_HEX_LETTER,
            TokenType::OTHER_ASCII_LETTER,
            TokenType::OTHER_ASCII_LETTER,
            TokenType::SMALL_O,
        ];
        $actualLexemeTypeLog = $listener->getLexemeTypeLog();
        self::assertSame($expectedLexemeTypeLog, $actualLexemeTypeLog);
        $expectedSymbolLog = [
            ProductionType::NT_PARTS,
            ProductionType::NT_PART,
            ProductionType::NT_ITEM,
            ProductionType::NT_ITEM_BODY,
            ProductionType::NT_SYMBOL,
            ProductionType::NT_UNESC_SYMBOL,
            ProductionType::T_OTHER_ASCII_LETTER,
            ProductionType::NT_ITEM_QUANT,
            ProductionType::NT_PART,
            ProductionType::NT_ITEM,
            ProductionType::NT_ITEM_BODY,
            ProductionType::NT_SYMBOL,
            ProductionType::NT_UNESC_SYMBOL,
            ProductionType::T_OTHER_HEX_LETTER,
            ProductionType::NT_ITEM_QUANT,
            ProductionType::NT_PART,
            ProductionType::NT_ITEM,
            ProductionType::NT_ITEM_BODY,
            ProductionType::NT_SYMBOL,
            ProductionType::NT_UNESC_SYMBOL,
            ProductionType::T_OTHER_ASCII_LETTER,
            ProductionType::NT_ITEM_QUANT,
            ProductionType::NT_PART,
            ProductionType::NT_ITEM,
            ProductionType::NT_ITEM_BODY,
            ProductionType::NT_SYMBOL,
            ProductionType::NT_UNESC_SYMBOL,
            ProductionType::T_OTHER_ASCII_LETTER,
            ProductionType::NT_ITEM_QUANT,
            ProductionType::NT_PART,
            ProductionType::NT_ITEM,
            ProductionType::NT_ITEM_BODY,
            ProductionType::NT_SYMBOL,
            ProductionType::NT_UNESC_SYMBOL,
            ProductionType::T_SMALL_O,
            ProductionType::NT_ITEM_QUANT,
            ProductionType::NT_PART,
            ProductionType::NT_ALT_PARTS,
            ProductionType::T_EOI,
        ];
        $actualSymbolLog = $listener->getSymbolLog();
        self::assertSame($expectedSymbolLog, $actualSymbolLog);
    }
}
