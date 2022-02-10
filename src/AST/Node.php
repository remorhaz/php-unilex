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
    private $id;

    private $name;

    private $attributeMap = [];

    private $childMap = [];

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
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
     * @param string $name
     * @param        $value
     * @return Node
     * @throws UniLexException
     */
    public function setAttribute(string $name, $value): self
    {
        if (isset($this->attributeMap[$name])) {
            throw new UniLexException("Attribute '{$name}' is already defined in syntax tree node {$this->getId()}");
        }
        $this->attributeMap[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws UniLexException
     */
    public function getAttribute(string $name)
    {
        if (!isset($this->attributeMap[$name])) {
            throw new UniLexException("Attribute '{$name}' is not defined in syntax tree node {$this->getId()}");
        }

        return $this->attributeMap[$name];
    }

    /**
     * @param string $name
     * @return int
     * @throws UniLexException
     */
    public function getIntAttribute(string $name): int
    {
        $attribute = $this->getAttribute($name);
        if (is_int($attribute)) {
            return $attribute;
        }

        throw new Exception\InvalidAttributeException($name, $attribute, 'integer');
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
                if (!is_int($symbol) || $symbol > 0xFF) {
                    throw new Exception\InvalidAttributeException($name, $attribute, 'integer[]');
                }

                $string .= chr($symbol);
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
        if (!isset($this->childMap[$index])) {
            throw new UniLexException("Child node at index {$index} in node {$this->getId()} is not defined");
        }

        return $this->childMap[$index];
    }

    /**
     * @return Node[]
     */
    public function getChildList(): array
    {
        return $this->childMap;
    }

    /**
     * @return int[]
     */
    public function getChildIndexList(): array
    {
        return array_keys($this->childMap);
    }

    public function getAttributeList(): array
    {
        return $this->attributeMap;
    }
}
