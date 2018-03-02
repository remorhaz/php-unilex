<?php

namespace Remorhaz\UniLex\Test\LL1Parser\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\LL1Parser\Lookup\Table;

class TableTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testAddProduction_ProductionNotSet_GetProductionReturnsAddedProduction(): void
    {
        $table = new Table;
        $production = [3, 4];
        $table->addProduction(1, 2, ...$production);
        $actualValue = $table->getProduction(1, 2);
        self::assertSame($production, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Production for [1:2] is already defined
     */
    public function testAddProduction_ProductionSet_ThrowsException(): void
    {
        $table = new Table;
        $production = [3, 4];
        $table->addProduction(1, 2, ...$production);
        $table->addProduction(1, 2, ...$production);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Production for [1:2] is not defined
     */
    public function testGetProduction_ProductionNotSet_ThrowsException(): void
    {
        $table = new Table;
        $table->getProduction(1, 2);
    }

    public function testHasProduction_ProductionNotSet_ReturnsFalse(): void
    {
        $table = new Table;
        $actualValue = $table->hasProduction(1, 2);
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testHasProduction_ProductionSet_ReturnsTrue(): void
    {
        $table = new Table;
        $table->addProduction(1, 2, ...[3, 4]);
        $actualValue = $table->hasProduction(1, 2);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testExportMap_ProductionsSet_ReturnsMatchingValue(): void
    {
        $table = new Table;
        $table->addProduction(1, 2, ...[3, 4]);
        $table->addProduction(1, 3, ...[4]);
        $table->addProduction(2, 2, ...[]);
        $expectedValue = [
            1 => [
                2 => [3, 4],
                3 => [4],
            ],
            2 => [
                2 => [],
            ],
        ];
        $actualValue = $table->exportMap();
        self::assertEquals($expectedValue, $actualValue);
    }
}
