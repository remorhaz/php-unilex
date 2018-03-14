<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Parser\LL1\SDD\RuleSetApplier;
use Remorhaz\UniLex\Parser\LL1\SDD\RuleSetLoader;
use Remorhaz\UniLex\RegExp\Grammar\SDD\ConfigFile as SDDConfigFile;
use Remorhaz\UniLex\Parser\SyntaxTree\Tree;
use Remorhaz\UniLex\Parser\SyntaxTree\SDD\ContextFactory;
use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\TokenReader;
use Remorhaz\UniLex\Parser\LL1\Parser;
use Remorhaz\UniLex\RegExp\Grammar\ConfigFile;
use Remorhaz\UniLex\RegExp\Grammar\SymbolType;
use Remorhaz\UniLex\RegExp\Grammar\TokenType;
use Remorhaz\UniLex\RegExp\TokenMatcher;
use Remorhaz\UniLex\Parser\LL1\ParseTreeBuilder;
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
        $tree = new Tree;
        $treeBuilder = new ParseTreeBuilder($grammar, $tree);
        $parser = new Parser($grammar, $reader, SymbolType::NT_ROOT, $treeBuilder);
        $parser->run();
        $actualValue = $this->walkParseTree($tree);
        $expectedValue = [
            ['symbol', SymbolType::NT_ROOT],
            ['symbol', SymbolType::NT_PARTS],
            ['symbol', SymbolType::NT_PART],
            ['symbol', SymbolType::NT_ITEM],
            ['symbol', SymbolType::NT_ITEM_BODY],
            ['symbol', SymbolType::NT_SYMBOL],
            ['symbol', SymbolType::NT_UNESC_SYMBOL],
            ['symbol', SymbolType::T_OTHER_ASCII_LETTER],
            ['token', TokenType::OTHER_ASCII_LETTER],
            ['symbol', SymbolType::NT_ITEM_QUANT],
            ['symbol', SymbolType::NT_MORE_ITEMS],
            ['symbol', SymbolType::NT_ITEM],
            ['symbol', SymbolType::NT_ITEM_BODY],
            ['symbol', SymbolType::NT_SYMBOL],
            ['symbol', SymbolType::NT_UNESC_SYMBOL],
            ['symbol', SymbolType::T_OTHER_HEX_LETTER],
            ['token', TokenType::OTHER_HEX_LETTER],
            ['symbol', SymbolType::NT_ITEM_QUANT],
            ['symbol', SymbolType::NT_MORE_ITEMS_TAIL],
            ['symbol', SymbolType::NT_ITEM],
            ['symbol', SymbolType::NT_ITEM_BODY],
            ['symbol', SymbolType::NT_SYMBOL],
            ['symbol', SymbolType::NT_UNESC_SYMBOL],
            ['symbol', SymbolType::T_OTHER_ASCII_LETTER],
            ['token', TokenType::OTHER_ASCII_LETTER],
            ['symbol', SymbolType::NT_ITEM_QUANT],
            ['symbol', SymbolType::NT_MORE_ITEMS_TAIL],
            ['symbol', SymbolType::NT_ITEM],
            ['symbol', SymbolType::NT_ITEM_BODY],
            ['symbol', SymbolType::NT_SYMBOL],
            ['symbol', SymbolType::NT_UNESC_SYMBOL],
            ['symbol', SymbolType::T_OTHER_ASCII_LETTER],
            ['token', TokenType::OTHER_ASCII_LETTER],
            ['symbol', SymbolType::NT_ITEM_QUANT],
            ['symbol', SymbolType::NT_MORE_ITEMS_TAIL],
            ['symbol', SymbolType::NT_ITEM],
            ['symbol', SymbolType::NT_ITEM_BODY],
            ['symbol', SymbolType::NT_SYMBOL],
            ['symbol', SymbolType::NT_UNESC_SYMBOL],
            ['symbol', SymbolType::T_SMALL_O],
            ['token', TokenType::SMALL_O],
            ['symbol', SymbolType::NT_ITEM_QUANT],
            ['symbol', SymbolType::NT_MORE_ITEMS_TAIL],
            ['symbol', SymbolType::NT_ALT_PARTS],
            ['symbol', SymbolType::T_EOI],
            ['token', TokenType::EOI],
        ];
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @param Tree $tree
     * @return array
     * @throws \Remorhaz\UniLex\Exception
     */
    private function walkParseTree(Tree $tree): array
    {
        $result = [];
        foreach ($tree->walk() as $node) {
            switch ($node->getName()) {
                case 'symbol':
                    $result[] = [$node->getName(), $node->getAttribute('id')];
                    break;

                case 'token':
                    /** @var Token $token */
                    $token = $node->getAttribute('token');
                    $result[] = [$node->getName(), $token->getType()];
                    break;

                default:
                    $result[] = [$node->getName()];
            }
        }
        return $result;
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @todo Experimental test
     */
    public function testParserSemantic(): void
    {
        $buffer = CharBufferFactory::createFromUtf8String('a{12,14}bc');
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $reader = new TokenReader($buffer, new TokenMatcher, new TokenFactory($grammar));
        $tree = new Tree;
        $treeRuleSet = RuleSetLoader::loadFile(new ContextFactory($tree), SDDConfigFile::getPath());
        $treeBuilder = new RuleSetApplier($treeRuleSet);
        $parser = new Parser($grammar, $reader, SymbolType::NT_ROOT, $treeBuilder);
        $parser->run();
        $actualValue = $tree
            ->getRootNode()
            ->getName();
        self::assertSame('concatenate', $actualValue);
    }
}
