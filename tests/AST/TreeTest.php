<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\AST;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UniLexException;

/**
 * @covers \Remorhaz\UniLex\AST\Tree
 */
class TreeTest extends TestCase
{
    /**
     * @throws UniLexException
     */
    public function testGetNode_NodeNotExists_ThrowsException(): void
    {
        $tree = new Tree();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Node 0 is not defined in syntax tree');
        $tree->getNode(0);
    }

    /**
     * @throws UniLexException
     */
    public function testGetNode_NodeExists_ReturnsMatchingNode(): void
    {
        $tree = new Tree();
        $node = $tree->createNode('a');
        $actualValue = $tree->getNode($node->getId());
        self::assertSame($node, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetRootNode_RootNodeNotSet_ThrowsException(): void
    {
        $tree = new Tree();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Root node of syntax tree is undefined');
        $tree->getRootNode();
    }

    /**
     * @throws UniLexException
     */
    public function testSetRootNode_RootNodeNotSet_GetRootNodeReturnsMatchingNode(): void
    {
        $tree = new Tree();
        $node = $tree->createNode('a');
        $tree->setRootNode($node);
        $actualValue = $tree->getRootNode();
        self::assertSame($node, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testSetRootNode_RootNodeSet_ThrowsException(): void
    {
        $tree = new Tree();
        $node = $tree->createNode('a');
        $tree->setRootNode($node);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Root node of syntax tree is already set');
        $tree->setRootNode($node);
    }
}
