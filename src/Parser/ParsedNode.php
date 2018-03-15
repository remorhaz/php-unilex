<?php

namespace Remorhaz\UniLex\Parser;

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
        if (!array_key_exists($name, $this->attributeList)) {
            throw new Exception("Attribute '{$name}' not defined in node {$this->getIndex()}");
        }
        return $this->attributeList[$name];
    }

    /**
     * @param string $name
     * @param $value
     * @return ParsedNode
     * @throws Exception
     */
    public function setAttribute(string $name, $value)
    {
        if (array_key_exists($name, $this->attributeList)) {
            throw new Exception("Attribute '{$name}' is already defined in node {$this->getIndex()}");
        }
        $this->attributeList[$name] = $value;
        return $this;
    }
}