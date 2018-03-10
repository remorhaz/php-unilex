<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\Exception;

class SyntaxTreeNode
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

    /**
     * @param string $name
     * @param $value
     * @throws Exception
     */
    public function setAttribute(string $name, $value): void
    {
        if (isset($this->attributeMap[$name])) {
            throw new Exception("Attribute '{$name}' is already defined in syntax tree node {$this->getId()}");
        }
        $this->attributeMap[$name] = $value;
    }

    public function addChild(SyntaxTreeNode $node): void
    {
        $this->childMap[] = $node;
    }
}
