<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\ParserTable;
use Remorhaz\UniLex\RegExp\ProductionType;

class ParserTableTest extends TestCase
{

    public function testIsTerminal_TerminalProduction_ReturnsTrue(): void
    {
        $actualValue = (new ParserTable)->isTerminal(ProductionType::GROUP_START);
        self::assertTrue($actualValue);
    }

    public function testIsTerminal_NonTerminalProduction_ReturnsFalse(): void
    {
        $actualValue = (new ParserTable)->isTerminal(ProductionType::GROUP);
        self::assertFalse($actualValue);
    }
}
