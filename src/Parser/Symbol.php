<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser;

use Remorhaz\UniLex\AttributeListInterface;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Stack\StackableSymbolInterface;

use function array_key_exists;

class Symbol implements StackableSymbolInterface, AttributeListInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $attributeList = [];

    public function __construct(
        private readonly int $index,
        private readonly int $symbolId,
    ) {
    }

    public function getSymbolId(): int
    {
        return $this->symbolId;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @throws Exception
     */
    public function getAttribute(string $name): mixed
    {
        return $this->attributeExists($name)
            ? $this->attributeList[$name]
            : throw new Exception("Attribute '$name' not defined in node {$this->getIndex()}");
    }

    /**
     * @throws Exception
     */
    public function setAttribute(string $name, mixed $value): static
    {
        $this->attributeList[$name] = $this->attributeExists($name)
            ? throw new Exception("Attribute '$name' is already defined in node {$this->getIndex()}")
            : $value;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function attributeExists(string $name): bool
    {
        return array_key_exists($name, $this->attributeList);
    }

    public function getShortcut(): AttributeListShortcut
    {
        return new AttributeListShortcut($this);
    }
}
