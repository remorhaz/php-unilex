<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\RegExp\AST\NodeType;
use Remorhaz\UniLex\RegExp\FSM\StateMap;
use Remorhaz\UniLex\RegExp\FSM\StateMapBuilder;
use Remorhaz\UniLex\Stack\SymbolStack;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\StateMapBuilder
 */
class StateMapBuilderTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unknown AST node name: unknown
     */
    public function testOnBeginProduction_UnknownNodeName_ThrowsException(): void
    {
        $node = new Node(1, 'unknown');
        $builder = new StateMapBuilder(new StateMap);
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
        $builder = new StateMapBuilder(new StateMap);
        $stack = new SymbolStack;
        $builder->onBeginProduction($node, $stack);
    }

    /**
     * @param string $name
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerTerminalNodeNames
     */
    public function testOnBeginProduction_TerminalNode_PushesNothingToStack(string $name): void
    {
        $node = new Node(1, $name);
        $builder = new StateMapBuilder(new StateMap);
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
    public function testOnFinishProduction_SymbolNode_AddsMatchingTransition(): void
    {
        $stateMap = new StateMap;
        $builder = new StateMapBuilder($stateMap);
        $node = new Node(1, NodeType::SYMBOL);
        $node->setAttribute('code', 0x61);
        $stateIn = $stateMap->createState();
        $node->setAttribute('i.state_in', $stateIn);
        $stateOut = $stateMap->createState();
        $node->setAttribute('i.state_out', $stateOut);
        $builder->onFinishProduction($node);
        $expectedCharTransitionList[$stateIn][$stateOut] = [[0x61, 0x61]];
        self::assertEquals($expectedCharTransitionList, $stateMap->getCharTransitionList());
        self::assertEquals([], $stateMap->getEpsilonTransitionList());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testOnFinishProduction_EmptyNode_AddsMatchingTransition(): void
    {
        $stateMap = new StateMap;
        $builder = new StateMapBuilder($stateMap);
        $node = new Node(1, NodeType::EMPTY);
        $stateIn = $stateMap->createState();
        $node->setAttribute('i.state_in', $stateIn);
        $stateOut = $stateMap->createState();
        $node->setAttribute('i.state_out', $stateOut);
        $builder->onFinishProduction($node);
        $expectedEpsilonTransitionList[$stateIn][$stateOut] = true;
        self::assertEquals([], $stateMap->getCharTransitionList());
        self::assertEquals($expectedEpsilonTransitionList, $stateMap->getEpsilonTransitionList());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testOnFinishProduction_SymbolAnyNode_AddsMatchingTransition(): void
    {
        $stateMap = new StateMap;
        $builder = new StateMapBuilder($stateMap);
        $node = new Node(1, NodeType::SYMBOL_ANY);
        $stateIn = $stateMap->createState();
        $node->setAttribute('i.state_in', $stateIn);
        $stateOut = $stateMap->createState();
        $node->setAttribute('i.state_out', $stateOut);
        $builder->onFinishProduction($node);
        $expectedCharTransitionList[$stateIn][$stateOut] = [[0x00, 0x10FFFF]];
        self::assertEquals($expectedCharTransitionList, $stateMap->getCharTransitionList());
        self::assertEquals([], $stateMap->getEpsilonTransitionList());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testOnStartProduction_NodeWithoutStateAttributes_NodeHasPositiveStateInAttribute(): void
    {
        $stateMap = new StateMap;
        $builder = new StateMapBuilder($stateMap);
        $node = new Node(1, NodeType::SYMBOL);
        $builder->onStart($node);
        self::assertGreaterThan(0, $node->getAttribute('i.state_in'));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testOnStartProduction_NodeWithoutStateAttributes_NodeHasPositiveStateOutAttribute(): void
    {
        $stateMap = new StateMap;
        $builder = new StateMapBuilder($stateMap);
        $node = new Node(1, NodeType::SYMBOL);
        $builder->onStart($node);
        self::assertGreaterThan(0, $node->getAttribute('i.state_out'));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testOnStartProduction_NodeWithoutStateAttributes_NodeHasNotEqualStateAttributes(): void
    {
        $stateMap = new StateMap;
        $builder = new StateMapBuilder($stateMap);
        $node = new Node(1, NodeType::SYMBOL);
        $builder->onStart($node);
        self::assertNotEquals($node->getAttribute('i.state_in'), $node->getAttribute('i.state_out'));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testOnStartProduction_NodeWithoutStateAttributes_NodeStateInAttributeIsStartState(): void
    {
        $stateMap = new StateMap;
        $builder = new StateMapBuilder($stateMap);
        $node = new Node(1, NodeType::SYMBOL);
        $builder->onStart($node);
        self::assertEquals($stateMap->getStartState(), $node->getAttribute('i.state_in'));
    }
}
