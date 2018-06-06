<?php

namespace Remorhaz\UniLex\Parser;

use Remorhaz\UniLex\AttributeListInterface;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Stack\StackableSymbolInterface;

class Symbol implements StackableSymbolInterface, AttributeListInterface
{

    private $symbolId;

    private $attributeList = [];

    private $index;

    public function __construct(int $index, int $symbolId)
    {
        $this->index = $index;
        $this->symbolId = $symbolId;
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
     * @return $this
     * @throws Exception
     */
    public function setAttribute(string $name, $value)
    {
        if ($this->attributeExists($name)) {
            throw new Exception("Attribute '{$name}' is already defined in node {$this->getIndex()}");
        }
        $this->attributeList[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function attributeExists(string $name): bool
    {
        return isset($this->attributeList[$name]);
    }

    /**
     * @return array|AttributeListShortcut
     */
    public function getShortcut(): AttributeListShortcut
    {
        return new AttributeListShortcut($this);
    }
}
