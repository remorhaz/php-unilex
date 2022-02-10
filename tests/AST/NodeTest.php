<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\AST;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\Exception\InvalidAttributeException;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\Exception as UniLexException;

/**
 * @covers \Remorhaz\UniLex\AST\Node
 */
class NodeTest extends TestCase
{
    public function testGetId_ConstructedWithValue_ReturnsSameValue(): void
    {
        $actualValue = (new Node(1, 'a'))->getId();
        self::assertSame(1, $actualValue);
    }

    public function testGetName_ConstructedWithValue_ReturnsSameValue(): void
    {
        $actualValue = (new Node(1, 'a'))->getName();
        self::assertSame('a', $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testSetAttribute_AttributeNotExists_GetAttributeReturnsMatchingValue(): void
    {
        $node = new Node(1, 'a');
        $node->setAttribute('b', 'c');
        $actualValue = $node->getAttribute('b');
        self::assertSame('c', $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testSetAttribute_AttributeExists_ThrowsException(): void
    {
        $node = new Node(1, 'a');
        $node->setAttribute('b', 'c');

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Attribute \'b\' is already defined in syntax tree node 1');
        $node->setAttribute('b', 'd');
    }

    /**
     * @throws UniLexException
     */
    public function testGetAttribute_AttributeNotExists_ThrowsException(): void
    {
        $node = new Node(1, 'a');

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Attribute \'b\' is not defined in syntax tree node 1');
        $node->getAttribute('b');
    }

    /**
     * @throws UniLexException
     */
    public function testGetIntAttribute_AttributeNotExists_ThrowsException(): void
    {
        $node = new Node(1, 'a');

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Attribute \'b\' is not defined in syntax tree node 1');
        $node->getIntAttribute('b');
    }

    /**
     * @throws UniLexException
     */
    public function testGetIntAttribute_IntegerAttributeExists_ReturnsAttributeValue(): void
    {
        $node = new Node(1, 'a');
        $node->setAttribute('b', 2);

        self::assertSame(2, $node->getIntAttribute('b'));
    }

    /**
     * @throws UniLexException
     */
    public function testGetIntAttribute_NonIntegerAttributeExists_ReturnsAttributeValue(): void
    {
        $node = new Node(1, 'a');
        $node->setAttribute('b', 'c');

        $this->expectException(InvalidAttributeException::class);
        $this->expectExceptionMessage("Node attribute 'b' has invalid type: string instead of expected integer");
        $node->getIntAttribute('b');
    }

    /**
     * @throws UniLexException
     */
    public function testGetChild_NoChildren_ThrowsException(): void
    {
        $node = new Node(1, 'a');

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Child node at index 0 in node 1 is not defined');
        $node->getChild(0);
    }

    /**
     * @throws UniLexException
     */
    public function testGetChild_ChildNotExists_ThrowsException(): void
    {
        $node = new Node(1, 'a');
        $childNode = new Node(2, 'b');
        $node->addChild($childNode);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Child node at index 1 in node 1 is not defined');
        $node->getChild(1);
    }

    /**
     * @throws UniLexException
     */
    public function testAddChild_NoChildren_GetChildReturnsMatchingNode(): void
    {
        $node = new Node(1, 'a');
        $childNode = new Node(2, 'b');
        $node->addChild($childNode);
        $actualValue = $node->getChild(0);
        self::assertSame($childNode, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testAddChild_ChildExists_GetChildReturnsMatchingNode(): void
    {
        $node = new Node(1, 'a');
        $firstChildNode = new Node(2, 'b');
        $secondChildNode = new Node(3, 'c');
        $node->addChild($firstChildNode);
        $node->addChild($secondChildNode);
        $actualValue = $node->getChild(1);
        self::assertSame($secondChildNode, $actualValue);
    }

    public function testGetChildList_NoChildren_ReturnsEmptyArray(): void
    {
        $node = new Node(1, 'a');
        $actualValue = $node->getChildList();
        self::assertSame([], $actualValue);
    }

    public function testGetChildList_TwoChildrenAdded_ReturnsBothChildren(): void
    {
        $node = new Node(1, 'a');
        $firstChildNode = new Node(2, 'b');
        $secondChildNode = new Node(3, 'c');
        $node->addChild($firstChildNode);
        $node->addChild($secondChildNode);
        $actualValue = $node->getChildList();
        self::assertSame([$firstChildNode, $secondChildNode], $actualValue);
    }

    public function testGetAttributeList_NoAttributes_ReturnsEmptyArray(): void
    {
        $node = new Node(1, 'a');
        $actualValue = $node->getAttributeList();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetAttributeList_TwoAttributesSet_ReturnsAtributeMap(): void
    {
        $node = new Node(1, 'a');
        $node->setAttribute('b', 'c');
        $node->setAttribute('d', 'e');
        $expectedValue = ['b' => 'c', 'd' => 'e'];
        $actualValue = $node->getAttributeList();
        self::assertEquals($expectedValue, $actualValue);
    }
}
