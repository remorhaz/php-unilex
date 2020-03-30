<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\RegExp\AST\NodeType;
use Remorhaz\UniLex\RegExp\FSM\Nfa;
use Remorhaz\UniLex\RegExp\FSM\NfaBuilder;
use Remorhaz\UniLex\RegExp\PropertyLoaderInterface;
use Remorhaz\UniLex\Stack\SymbolStack;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\NfaBuilder
 */
class NfaBuilderTest extends TestCase
{

    /**
     * @throws UniLexException
     */
    public function testOnBeginProduction_UnknownNodeName_ThrowsException(): void
    {
        $node = new Node(1, 'unknown');
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $stack = new SymbolStack();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Unknown AST node name: unknown');
        $builder->onBeginProduction($node, $stack);
    }

    /**
     * @param string $name
     * @throws UniLexException
     * @dataProvider providerTerminalNodeNames
     */
    public function testOnBeginProduction_TerminalNodeWithChild_ThrowsException(string $name): void
    {
        $node = new Node(1, $name);
        $node->addChild(new Node(2, $node->getName()));
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $stack = new SymbolStack();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessageMatches('#^AST node \'.+\' should not have child nodes$#');
        $builder->onBeginProduction($node, $stack);
    }

    /**
     * @param string $name
     * @throws UniLexException
     * @dataProvider providerNotTerminalNodeNames
     */
    public function testOnBeginProduction_NotTerminalNodeWithoutChildren_ThrowsException(string $name): void
    {
        $node = new Node(1, $name);
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $stack = new SymbolStack();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessageMatches('#^AST node \'.+\' should have child nodes$#');
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
     * @throws UniLexException
     * @dataProvider providerTerminalNodeNames
     */
    public function testOnBeginProduction_TerminalNode_PushesNothingToStack(string $name): void
    {
        $node = new Node(1, $name);
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $stack = new SymbolStack();
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
     * @throws UniLexException
     */
    public function testOnStartProduction_NodeWithoutStateAttributes_NodeHasPositiveStateInAttribute(): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, NodeType::SYMBOL);
        $builder->onStart($node);
        self::assertGreaterThan(0, $node->getAttribute('state_in'));
    }

    /**
     * @throws UniLexException
     */
    public function testOnStartProduction_NodeWithoutStateAttributes_NodeHasPositiveStateOutAttribute(): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, NodeType::SYMBOL);
        $builder->onStart($node);
        self::assertGreaterThan(0, $node->getAttribute('state_out'));
    }

    /**
     * @throws UniLexException
     */
    public function testOnStartProduction_NodeWithoutStateAttributes_NodeHasNotEqualStateAttributes(): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, NodeType::SYMBOL);
        $builder->onStart($node);
        self::assertNotEquals($node->getAttribute('state_in'), $node->getAttribute('state_out'));
    }

    /**
     * @throws UniLexException
     */
    public function testOnStartProduction_NodeWithoutStateAttributes_NodeStateInAttributeIsStartState(): void
    {
        $nfa = new Nfa();
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder($nfa, $propertyLoader);
        $node = new Node(1, NodeType::SYMBOL);
        $builder->onStart($node);
        self::assertEquals($nfa->getStateMap()->getStartState(), $node->getAttribute('state_in'));
    }

    /**
     * @throws UniLexException
     */
    public function testOnFinishProduction_ControlSymbolWithInvalidCode_ThrowsException(): void
    {
        $nfa = new Nfa();
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder($nfa, $propertyLoader);
        $node = new Node(1, NodeType::SYMBOL_CTL);
        $node
            ->setAttribute('code', 0)
            ->setAttribute('state_in', $nfa->getStateMap()->createState())
            ->setAttribute('state_out', $nfa->getStateMap()->createState())
            ->setAttribute('in_range', false);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid control character: 0');
        $builder->onFinishProduction($node);
    }

    /**
     * @param string $name
     * @throws UniLexException
     * @dataProvider providerNotImplementedNodeNames
     */
    public function testOnBeginProduction_NotImplementedNode_ThrowsException(string $name): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, $name);
        $symbolStack = new SymbolStack();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessageMatches('#^AST nodes of type \'.+\' are not supported yet$#');
        $builder->onBeginProduction($node, $symbolStack);
    }

    public function providerNotImplementedNodeNames(): array
    {
        return [
            [NodeType::ASSERT],
        ];
    }

    /**
     * @throws UniLexException
     */
    public function testOnBeginProduction_RepeatNoteWithoutChildren_ThrowsException(): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, NodeType::REPEAT);
        $symbolStack = new SymbolStack();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('AST node \'repeat\' should have exactly one child node');
        $builder->onBeginProduction($node, $symbolStack);
    }

    /**
     * @throws UniLexException
     */
    public function testOnBeginProduction_RepeatNoteWithTwoChildren_ThrowsException(): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, NodeType::REPEAT);
        $node
            ->addChild(new Node(2, NodeType::EMPTY))
            ->addChild(new Node(3, NodeType::EMPTY));
        $symbolStack = new SymbolStack();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('AST node \'repeat\' should have exactly one child node');
        $builder->onBeginProduction($node, $symbolStack);
    }

    /**
     * @throws UniLexException
     */
    public function testOnBeginProduction_RepeatNoteWithFiniteMaxAttributeLessThanMinAttribute_ThrowsException(): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, NodeType::REPEAT);
        $node
            ->setAttribute('min', 2)
            ->setAttribute('max', 1)
            ->setAttribute('is_max_infinite', false)
            ->setAttribute('state_in', 1)
            ->setAttribute('state_out', 2)
            ->addChild(new Node(2, NodeType::EMPTY));
        $symbolStack = new SymbolStack();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('AST node \'repeat\' has invalid attributes: min(2) > max(1)');
        $builder->onBeginProduction($node, $symbolStack);
    }

    /**
     * @throws UniLexException
     */
    public function testOnBeginProduction_RangeNoteWithOneChild_ThrowsException(): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, NodeType::SYMBOL_RANGE);
        $node
            ->setAttribute('state_in', 1)
            ->setAttribute('state_out', 2)
            ->addChild(new Node(2, NodeType::SYMBOL));
        $symbolStack = new SymbolStack();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('AST node \'symbol_range\' should have exactly two child nodes');
        $builder->onBeginProduction($node, $symbolStack);
    }

    /**
     * @throws UniLexException
     */
    public function testOnBeginProduction_RangeNoteWithoutChildren_ThrowsException(): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, NodeType::SYMBOL_RANGE);
        $node
            ->setAttribute('state_in', 1)
            ->setAttribute('state_out', 2);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('AST node \'symbol_range\' should have exactly two child nodes');
        $builder->onBeginProduction($node, new SymbolStack());
    }

    /**
     * @throws UniLexException
     */
    public function testOnBeginProduction_RangeNoteWithThreeChild_ThrowsException(): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, NodeType::SYMBOL_RANGE);
        $node
            ->setAttribute('state_in', 1)
            ->setAttribute('state_out', 2)
            ->addChild(new Node(2, NodeType::SYMBOL))
            ->addChild(new Node(3, NodeType::SYMBOL))
            ->addChild(new Node(4, NodeType::SYMBOL));
        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('AST node \'symbol_range\' should have exactly two child nodes');
        $builder->onBeginProduction($node, new SymbolStack());
    }

    /**
     * @throws UniLexException
     */
    public function testOnFinishProduction_RangeNodeWithInvalidChars_ThrowsException(): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $rangeNode = new Node(1, NodeType::SYMBOL_RANGE);
        $startCharNode = new Node(2, NodeType::SYMBOL);
        $startCharNode->setAttribute('range_code', 2);
        $finishCharNode = new Node(3, NodeType::SYMBOL);
        $finishCharNode->setAttribute('range_code', 1);
        $rangeNode
            ->addChild($startCharNode)
            ->addChild($finishCharNode)
            ->setAttribute('in_range', false)
            ->setAttribute('state_in', 1)
            ->setAttribute('state_out', 2);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid range: start char is greater than finish char');
        $builder->onFinishProduction($rangeNode);
    }

    /**
     * @throws UniLexException
     */
    public function testOnFinishProduction_EmptyNodeInRange_ThrowsException(): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, NodeType::EMPTY);
        $node
            ->setAttribute('in_range', true)
            ->setAttribute('state_in', 1)
            ->setAttribute('state_out', 2);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid range component: no matching chars');
        $builder->onFinishProduction($node);
    }

    /**
     * @throws UniLexException
     */
    public function testOnFinishProduction_SymbolAnyNodeInRange_ThrowsException(): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, NodeType::SYMBOL_ANY);
        $node
            ->setAttribute('in_range', true)
            ->setAttribute('state_in', 1)
            ->setAttribute('state_out', 2);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid range component: any char is matching');
        $builder->onFinishProduction($node);
    }

    /**
     * @param int $code
     * @throws UniLexException
     * @dataProvider providerNotImplementedSimpleEscapeCodes
     */
    public function testOnFinishProduction_SimpleEscapeWithNotImplementedCode_ThrowsException(int $code): void
    {
        $propertyLoader = $this->createMock(PropertyLoaderInterface::class);
        $builder = new NfaBuilder(new Nfa(), $propertyLoader);
        $node = new Node(1, NodeType::ESC_SIMPLE);
        $node
            ->setAttribute('code', $code)
            ->setAttribute('state_in', 1)
            ->setAttribute('state_out', 2)
            ->setAttribute('in_range', false);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessageMatches('# is not implemented yet$#');
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
