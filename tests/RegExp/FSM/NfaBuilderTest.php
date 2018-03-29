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
            [NodeType::CONCATENATE],
            [NodeType::SYMBOL_CLASS],
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

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid control character: 0
     */
    public function testOnFinishProduction_ControlSymbolWithInvalidCode_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $builder = new NfaBuilder($stateMap);
        $node = new Node(1, NodeType::SYMBOL_CTL);
        $node
            ->setAttribute('code', 0)
            ->setAttribute('state_in', $stateMap->createState())
            ->setAttribute('state_out', $stateMap->createState());
        $builder->onFinishProduction($node);
    }

    /**
     * @param string $name
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessageRegExp #^AST nodes of type '.+' are not supported yet$#
     * @dataProvider providerNotImplementedNodeNames
     */
    public function testOnBeginProduction_NotImplementedNode_ThrowsException(string $name): void
    {
        $stateMap = new StateMap;
        $builder = new NfaBuilder($stateMap);
        $node = new Node(1, $name);
        $builder->onBeginProduction($node, new SymbolStack);
    }

    public function providerNotImplementedNodeNames(): array
    {
        return [
            [NodeType::ASSERT],
            [NodeType::SYMBOL_RANGE],
            [NodeType::SYMBOL_PROP],
        ];
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage AST node 'repeat' should have exactly one child node
     */
    public function testOnBeginProduction_RepeatNoteWithoutChildren_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $builder = new NfaBuilder($stateMap);
        $node = new Node(1, NodeType::REPEAT);
        $builder->onBeginProduction($node, new SymbolStack);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage AST node 'repeat' should have exactly one child node
     */
    public function testOnBeginProduction_RepeatNoteWithTwoChildren_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $builder = new NfaBuilder($stateMap);
        $node = new Node(1, NodeType::REPEAT);
        $node
            ->addChild(new Node(2, NodeType::EMPTY))
            ->addChild(new Node(3, NodeType::EMPTY));
        $builder->onBeginProduction($node, new SymbolStack);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage AST node 'repeat' has invalid attributes: min(2) > max(1)
     */
    public function testOnBeginProduction_RepeatNoteWithFiniteMaxAttributeLessThanMinAttribute_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $builder = new NfaBuilder($stateMap);
        $node = new Node(1, NodeType::REPEAT);
        $node
            ->setAttribute('min', 2)
            ->setAttribute('max', 1)
            ->setAttribute('is_max_infinite', false)
            ->setAttribute('state_in', 1)
            ->setAttribute('state_out', 2)
            ->addChild(new Node(2, NodeType::EMPTY));
        $builder->onBeginProduction($node, new SymbolStack);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessageRegExp # is not implemented yet$#
     * @dataProvider providerNotImplementedSimpleEscapeCodes
     */
    public function testOnFinishProduction_SimpleEscapeWithNotImplementedCode_ThrowsEsception(int $code): void
    {
        $stateMap = new StateMap;
        $builder = new NfaBuilder($stateMap);
        $node = new Node(1, NodeType::ESC_SIMPLE);
        $node
            ->setAttribute('code', $code)
            ->setAttribute('state_in', 1)
            ->setAttribute('state_out', 2);
        $builder->onFinishProduction($node);
    }

    public function providerNotImplementedSimpleEscapeCodes(): array
    {
        return [
            [0x41],
            [0x42],
            [0x43],
            [0x44],
            [0x45],
            [0x47],
            [0x48],
            [0x4B],
            [0x4E],
            [0x51],
            [0x52],
            [0x53],
            [0x56],
            [0x57],
            [0x58],
            [0x5A],
            [0x62],
            [0x64],
            [0x67],
            [0x68],
            [0x6B],
            [0x73],
            [0x76],
            [0x77],
            [0x7A],
        ];
    }
}
