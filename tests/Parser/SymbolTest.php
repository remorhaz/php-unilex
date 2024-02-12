<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Parser;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Parser\Symbol;

#[CoversClass(Symbol::class)]
class SymbolTest extends TestCase
{
    public function testGetIndex_ConstructedWithIndex_ReturnsSameValue(): void
    {
        $symbol = new Symbol(1, 2);
        self::assertSame(1, $symbol->getIndex());
    }

    public function testGetSymbolId_ConstructedWithSymbolId_ReturnsSameValue(): void
    {
        $symbol = new Symbol(1, 2);
        self::assertSame(2, $symbol->getSymbolId());
    }

    public function testGetAttribute_AttributeNotSet_ThrowsException(): void
    {
        $symbol = new Symbol(1, 2);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Attribute 'a' not defined in node 1");
        $symbol->getAttribute('a');
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerAttribute')]
    public function testGetAttribute_AttributeSet_ReturnsSameValue(mixed $value, mixed $expectedValue): void
    {
        $symbol = new Symbol(1, 2);
        $symbol->setAttribute('a', $value);
        self::assertSame($expectedValue, $symbol->getAttribute('a'));
    }

    /**
     * @return iterable<string, array{mixed, mixed}>
     */
    public static function providerAttribute(): iterable
    {
        return [
            'String' => ['b', 'b'],
            'Integer' => [3, 3],
            'Float' => [1.2, 1.2],
            'True' => [true, true],
            'False' => [false, false],
            'Null' => [null, null],
        ];
    }
}
