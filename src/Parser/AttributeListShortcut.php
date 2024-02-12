<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser;

use ArrayAccess;
use Remorhaz\UniLex\AttributeListInterface;
use Remorhaz\UniLex\Exception;

use function gettype;
use function is_string;

/**
 * @template ArrayAccess<string, mixed>
 */
class AttributeListShortcut implements ArrayAccess
{
    public function __construct(
        private readonly AttributeListInterface $attributeList,
    ) {
    }

    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset): mixed
    {
        return $this
            ->attributeList
            ->getAttribute($offset);
    }

    /**
     * @throws Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this
            ->attributeList
            ->setAttribute(
                is_string($offset)
                    ? $offset
                    : throw new Exception("Invalid attribute offset type: " . gettype($offset)),
                $value,
            );
    }

    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $offset): bool
    {
        return is_string($offset) && $this
            ->attributeList
            ->attributeExists($offset);
    }

    /**
     * @throws Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $offset): void
    {
        throw new Exception("Cannot remove symbol attribute");
    }
}
