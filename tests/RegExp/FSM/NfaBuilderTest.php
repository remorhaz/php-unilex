<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\RegExp\AST\NodeType;
use Remorhaz\UniLex\RegExp\FSM\StateMap;
use Remorhaz\UniLex\RegExp\FSM\NfaBuilder;
use Remorhaz\UniLex\Stack\SymbolStack;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\NfaBuilder
 */
class NfaBuilderTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unknown AST node name: unknown
     */
    public function testOnBeginProduction_UnknownNodeName_ThrowsException(): void
    {
        $node = new Node(1, 'unknown');
        $builder = new NfaBuilder(new StateMap);
        $stack = new SymbolStack;
        $builder->onBeginProduction($node, $stack);
    }

    /**
     * @param string $name
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessageRegExp #^AST node '.+' should not have child nodes$#
     * @dataProvider providerTerminalNodeNames
     */
    public function testOnBeginProduction_TerminalNodeWithChild_ThrowsException(string $name): void
    {
        $node = new Node(1, $name);
        $node->addChild(new Node(2, $node->getName()));
        $builder = new NfaBuilder(new StateMap);
        $stack = new SymbolStack;
        $builder->onBeginProduction($node, $stack);
    }

    /**
     * @param string $name
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessageRegExp #^AST node '.+' should have child nodes$#
     * @dataProvider providerNotTerminalNodeNames
     */
    public function testOnBeginProduction_NotTerminalNodeWithoutChildren_ThrowsException(string $name): void
    {
        $node = new Node(1, $name);
        $builder = new NfaBuilder(new StateMap);
        $stack = new SymbolStack;
        $builder->onBeginProduction($node, $stack);
    }

    public function providerNotTerminalNodeNames(): array
    {
        return [
            [NodeType::ALTERNATIVE],
        ];
    }

    /**
     * @param string $name
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerTerminalNodeNames
     */
    public function testOnBeginProduction_TerminalNode_PushesNothingToStack(string $name): void
    {
        $node = new Node(1, $name);
        $builder = new NfaBuilder(new StateMap);
        $stack = new SymbolStack;
        $builder->onBeginProduction($node, $stack);
        $actualValue = $stack->isEmpty();
        self::assertTrue($actualValue);
    }

    public function providerTerminalNodeNames(): array
    {
        return [
            [NodeType::SYMBOL],
            [NodeType::EMPTY],
            [NodeType::SYMBOL_ANY],
        ];
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testOnStartProduction_NodeWithoutStateAttributes_NodeHasPositiveStateInAttribute(): void
    {
        $stateMap = new StateMap;
        $builder = new NfaBuilder($stateMap);
        $node = new Node(1, NodeType::SYMBOL);
        $builder->onStart($node);
        self::assertGreaterThan(0, $node->getAttribute('state_in'));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testOnStartProduction_NodeWithoutStateAttributes_NodeHasPositiveStateOutAttribute(): void
    {
        $stateMap = new StateMap;
        $builder = new NfaBuilder($stateMap);
        $node = new Node(1, NodeType::SYMBOL);
        $builder->onStart($node);
        self::assertGreaterThan(0, $node->getAttribute('state_out'));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testOnStartProduction_NodeWithoutStateAttributes_NodeHasNotEqualStateAttributes(): void
    {
        $stateMap = new StateMap;
        $builder = new NfaBuilder($stateMap);
        $node = new Node(1, NodeType::SYMBOL);
        $builder->onStart($node);
        self::assertNotEquals($node->getAttribute('state_in'), $node->getAttribute('state_out'));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testOnStartProduction_NodeWithoutStateAttributes_NodeStateInAttributeIsStartState(): void
    {
        $stateMap = new StateMap;
        $builder = new NfaBuilder($stateMap);
        $node = new Node(1, NodeType::SYMBOL);
        $builder->onStart($node);
        self::assertEquals($stateMap->getStartState(), $node->getAttribute('state_in'));
    }
}
