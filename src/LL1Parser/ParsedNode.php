<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Exception;

abstract class ParsedNode
{

    private $index;

    private $attributeList = [];

    public function __construct(int $index)
    {
        $this->index = $index;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function getAttribute(string $name)
    {
        if (!isset($this->attributeList[$name])) {
            throw new Exception("Attribute '{$name}' not defined in node {$this->getIndex()}");
        }
        return $this->attributeList[$name];
    }

    /**
     * @param string $name
     * @param $value
     * @throws Exception
     */
    public function setAttribute(string $name, $value): void
    {
        if (isset($this->attributeList[$name])) {
            throw new Exception("Attribute '{$name}' is already defined in node {$this->getIndex()}");
        }
        $this->attributeList[$name] = $value;
    }
}
