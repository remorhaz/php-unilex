<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\LL1Parser\SDD\RuleSetApplier;
use Remorhaz\UniLex\LL1Parser\SDD\RuleSetLoader;
use Remorhaz\UniLex\RegExp\Grammar\SDD\ConfigFile as SDDConfigFile;
use Remorhaz\UniLex\RegExp\SyntaxTree;
use Remorhaz\UniLex\RegExp\SyntaxTreeContextFactory;
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
    public function testParserA()
    {
        $buffer = CharBufferFactory::createFromUtf8String('hello');
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $reader = new TokenReader($buffer, new TokenMatcher, new TokenFactory($grammar));
        $treeBuilder = new ParseTreeBuilder($grammar, SymbolType::NT_ROOT);
        $parser = new Parser($grammar, $reader, SymbolType::NT_ROOT, $treeBuilder);
        $parser->run();
        $expectedTokenTypeLog = [
            TokenType::OTHER_ASCII_LETTER,
            TokenType::OTHER_HEX_LETTER,
            TokenType::OTHER_ASCII_LETTER,
            TokenType::OTHER_ASCII_LETTER,
            TokenType::SMALL_O,
        ];
        $actualTokenTypeLog = $treeBuilder->getTokenTypeLog();
        self::assertSame($expectedTokenTypeLog, $actualTokenTypeLog);
        $expectedSymbolLog = [
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
        $actualSymbolLog = $treeBuilder->getSymbolLog();
        self::assertSame($expectedSymbolLog, $actualSymbolLog);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @todo Experimental test
     */
    public function testParserSemantic(): void
    {
        $buffer = CharBufferFactory::createFromUtf8String('a{12,2}');
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $reader = new TokenReader($buffer, new TokenMatcher, new TokenFactory($grammar));
        $tree = new SyntaxTree;
        $treeContextFactory = new SyntaxTreeContextFactory($tree);
        $treeRuleSet = RuleSetLoader::loadFile($treeContextFactory, SDDConfigFile::getPath());
        $treeBuilder = new RuleSetApplier($treeRuleSet);
        $parser = new Parser($grammar, $reader, SymbolType::NT_ROOT, $treeBuilder);
        $parser->run();
        $actualValue = $tree->getRootNode()
            ->getName();
        self::assertSame('alternative', $actualValue);
        //var_export($tree->getRootNode());
    }
}
