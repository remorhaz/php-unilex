<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\TokenReader;
use Remorhaz\UniLex\LL1Parser\Parser;
use Remorhaz\UniLex\RegExp\Grammar\ConfigFile;
use Remorhaz\UniLex\RegExp\Grammar\SymbolType;
use Remorhaz\UniLex\RegExp\Grammar\TokenType;
use Remorhaz\UniLex\RegExp\TokenMatcher;
use Remorhaz\UniLex\LL1Parser\ParseTreeBuilder;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

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
        $buffer = CharBufferFactory::createFromUtf8String('hello');
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $reader = new TokenReader($buffer, new TokenMatcher, new TokenFactory($grammar));
        $listener = new ParseTreeBuilder($grammar, SymbolType::NT_ROOT);
        $parser = new Parser($grammar, $reader, SymbolType::NT_ROOT, $listener);
        $parser->run();
        $expectedTokenTypeLog = [
            TokenType::OTHER_ASCII_LETTER,
            TokenType::OTHER_HEX_LETTER,
            TokenType::OTHER_ASCII_LETTER,
            TokenType::OTHER_ASCII_LETTER,
            TokenType::SMALL_O,
        ];
        $actualTokenTypeLog = $listener->getTokenTypeLog();
        self::assertSame($expectedTokenTypeLog, $actualTokenTypeLog);
        $expectedSymbolLog = [
            SymbolType::NT_ROOT,
            SymbolType::NT_PARTS,
            SymbolType::NT_PART,
            SymbolType::NT_ITEM,
            SymbolType::NT_ITEM_BODY,
            SymbolType::NT_SYMBOL,
            SymbolType::NT_UNESC_SYMBOL,
            SymbolType::T_OTHER_ASCII_LETTER,
            SymbolType::NT_ITEM_QUANT,
            SymbolType::NT_PART,
            SymbolType::NT_ITEM,
            SymbolType::NT_ITEM_BODY,
            SymbolType::NT_SYMBOL,
            SymbolType::NT_UNESC_SYMBOL,
            SymbolType::T_OTHER_HEX_LETTER,
            SymbolType::NT_ITEM_QUANT,
            SymbolType::NT_PART,
            SymbolType::NT_ITEM,
            SymbolType::NT_ITEM_BODY,
            SymbolType::NT_SYMBOL,
            SymbolType::NT_UNESC_SYMBOL,
            SymbolType::T_OTHER_ASCII_LETTER,
            SymbolType::NT_ITEM_QUANT,
            SymbolType::NT_PART,
            SymbolType::NT_ITEM,
            SymbolType::NT_ITEM_BODY,
            SymbolType::NT_SYMBOL,
            SymbolType::NT_UNESC_SYMBOL,
            SymbolType::T_OTHER_ASCII_LETTER,
            SymbolType::NT_ITEM_QUANT,
            SymbolType::NT_PART,
            SymbolType::NT_ITEM,
            SymbolType::NT_ITEM_BODY,
            SymbolType::NT_SYMBOL,
            SymbolType::NT_UNESC_SYMBOL,
            SymbolType::T_SMALL_O,
            SymbolType::NT_ITEM_QUANT,
            SymbolType::NT_PART,
            SymbolType::NT_ALT_PARTS,
            SymbolType::T_EOI,
        ];
        $actualSymbolLog = $listener->getSymbolLog();
        self::assertSame($expectedSymbolLog, $actualSymbolLog);
    }
}
