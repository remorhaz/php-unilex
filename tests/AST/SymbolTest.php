<?php

namespace Remorhaz\UniLex\Test\AST;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\AST\Symbol;
use Remorhaz\UniLex\Exception;

/**
 * @covers \Remorhaz\UniLex\AST\Symbol
 */
class SymbolTest extends TestCase
{

    public function testGetHeader_ConstructedWithValue_ReturnsSameValue(): void
    {
        $node = new Node(1, 'a');
        $actualValue = (new Symbol($node, 0))->getHeader();
        self::assertSame($node, $actualValue);
    }

    public function testGetIndex_ConstructedWithValue_ReturnsSameValue(): void
    {
        $node = new Node(1, 'a');
        $actualValue = (new Symbol($node, 0))->getIndex();
        self::assertSame(0, $actualValue);
    }

    /**
     * @throws Exception
     */
    public function testGetChild_ConstructedWithNodeAndIndex_ReturnsMatchingChildNode(): void
    {
        $node = new Node(1, 'a');
        $childNode = new Node(2, 'b');
        $node->addChild($childNode);
        $actualValue = (new Symbol($node, 0))->getSymbol();
        self::assertSame($childNode, $actualValue);
    }
}
