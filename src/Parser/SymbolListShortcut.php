<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser;

use ArrayAccess;
use Remorhaz\UniLex\Exception;

use function is_int;

/**
 * @template ArrayAccess<int, AttributeListShortcut>
 */
class SymbolListShortcut implements ArrayAccess
{
    public function __construct(
        private readonly Production $production,
    ) {
    }

    /**
     * @throws Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset): AttributeListShortcut
    {
        $symbol = $this
            ->production
            ->getSymbol($offset);

        return new AttributeListShortcut($symbol);
    }

    /**
     * @throws Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new Exception("Cannot change production symbol");
    }

    /**
     * @throws Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $offset): void
    {
        throw new Exception("Cannot remove production symbol");
    }

    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $offset): bool
    {
        return is_int($offset) && $this
            ->production
            ->symbolExists($offset);
    }
}
