<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\AST;

use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Stack\StackableSymbolInterface;

use function chr;
use function is_array;
use function is_int;

class Node implements StackableSymbolInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $attributeMap = [];

    /**
     * @var list<Node>
     */
    private array $childMap = [];

    public function __construct(
        private int $id,
        private string $name,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClone(): self
    {
        $clone = clone $this;
        $clone->childMap = [];
        foreach ($this->getChildList() as $childNode) {
            $clone->addChild($childNode->getClone());
        }

        return $clone;
    }

    /**
     * @throws UniLexException
     */
    public function setAttribute(string $name, mixed $value): self
    {
        $this->attributeMap[$name] = isset($this->attributeMap[$name])
            ? throw new UniLexException("Attribute '$name' is already defined in syntax tree node {$this->getId()}")
            : $value;

        return $this;
    }

    /**
     * @throws UniLexException
     */
    public function getAttribute(string $name): mixed
    {
        return $this->attributeMap[$name]
            ?? throw new UniLexException("Attribute '{$name}' is not defined in syntax tree node {$this->getId()}");
    }

    /**
     * @param string $name
     * @return int
     * @throws UniLexException
     */
    public function getIntAttribute(string $name): int
    {
        $attribute = $this->getAttribute($name);

        return is_int($attribute)
            ? $attribute
            : throw new Exception\InvalidAttributeException($name, $attribute, 'integer');
    }

    /**
     * @param string $name
     * @return string
     * @throws UniLexException
     */
    public function getStringAttribute(string $name): string
    {
        $attribute = $this->getAttribute($name);
        if (is_array($attribute)) {
            $string = '';
            foreach ($attribute as $symbol) {
                $string .= is_int($symbol) && $symbol <= 0xFF
                    ? chr($symbol)
                    : throw new Exception\InvalidAttributeException($name, $attribute, 'integer[]');
            }

            return $string;
        }

        throw new Exception\InvalidAttributeException($name, $attribute, 'integer[]');
    }

    public function addChild(Node $node): self
    {
        $this->childMap[] = $node;

        return $this;
    }

    /**
     * @param int $index
     * @return Node
     * @throws UniLexException
     */
    public function getChild(int $index): Node
    {
        return $this->childMap[$index]
            ?? throw new UniLexException("Child node at index {$index} in node {$this->getId()} is not defined");
    }

    /**
     * @return list<Node>
     */
    public function getChildList(): array
    {
        return $this->childMap;
    }

    /**
     * @return list<int>
     */
    public function getChildIndexList(): array
    {
        return array_keys($this->childMap);
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributeList(): array
    {
        return $this->attributeMap;
    }
}
