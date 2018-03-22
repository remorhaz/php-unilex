<?php

namespace Remorhaz\UniLex\Test\AST;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\Tree;

/**
 * @covers \Remorhaz\UniLex\AST\Tree
 */
class TreeTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Node 0 is not defined in syntax tree
     */
    public function testGetNode_NodeNotExists_ThrowsException(): void
    {
        (new Tree)->getNode(0);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetNode_NodeExists_ReturnsMatchingNode(): void
    {
        $tree = new Tree;
        $node = $tree->createNode('a');
        $actualValue = $tree->getNode($node->getId());
        self::assertSame($node, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Root node of syntax tree is undefined
     */
    public function testGetRootNode_RootNodeNotSet_ThrowsException(): void
    {
        (new Tree)->getRootNode();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testSetRootNode_RootNodeNotSet_GetRootNodeReturnsMatchingNode(): void
    {
        $tree = new Tree;
        $node = $tree->createNode('a');
        $tree->setRootNode($node);
        $actualValue = $tree->getRootNode();
        self::assertSame($node, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Root node of syntax tree is already set
     */
    public function testSetRootNode_RootNodeSet_ThrowsException(): void
    {
        $tree = new Tree;
        $node = $tree->createNode('a');
        $tree->setRootNode($node);
        $tree->setRootNode($node);
    }
}
