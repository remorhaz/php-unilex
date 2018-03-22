<?php

namespace Remorhaz\UniLex\Test\RegExp\AST;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\RegExp\AST\FsmBuilder;
use Remorhaz\UniLex\RegExp\AST\NodeType;
use Remorhaz\UniLex\Stack\SymbolStack;

/**
 * @covers \Remorhaz\UniLex\RegExp\AST\FsmBuilder
 */
class FsmBuilderTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unknown AST node name: unknown
     */
    public function testOnBeginProduction_UnknownNodeName_ThrowsException(): void
    {
        $node = new Node(1, 'unknown');
        $builder = new FsmBuilder;
        $stack = new SymbolStack;
        $builder->onBeginProduction($node, $stack);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage AST node 'symbol' should not have child nodes
     */
    public function testOnBeginProduction_TerminalNodeWithChild_ThrowsException(): void
    {
        $node = new Node(1, NodeType::SYMBOL);
        $node->addChild(new Node(2, $node->getName()));
        $builder = new FsmBuilder;
        $stack = new SymbolStack;
        $builder->onBeginProduction($node, $stack);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testOnBeginProduction_TerminalNode_PushesNothingToStack(): void
    {
        $node = new Node(1, NodeType::SYMBOL);
        $builder = new FsmBuilder;
        $stack = new SymbolStack;
        $builder->onBeginProduction($node, $stack);
        $actualValue = $stack->isEmpty();
        self::assertTrue($actualValue);
    }
}
