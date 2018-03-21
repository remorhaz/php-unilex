<?php

namespace Remorhaz\UniLex\Test\AST;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\AST\EopSymbol;
use Remorhaz\UniLex\AST\Node;

/**
 * @covers \Remorhaz\UniLex\AST\EopSymbol
 */
class EopSymbolTest extends TestCase
{

    public function testGetNode_CreatedWithValue_ReturnsSameValue(): void
    {
        $node = new Node(1, 'a');
        $actualValue = (new EopSymbol($node))->getNode();
        self::assertSame($node, $actualValue);
    }
}
