<?php

namespace Remorhaz\UniLex\Parser;

use ArrayAccess;
use Remorhaz\UniLex\AttributeListInterface;
use Remorhaz\UniLex\Exception;

class AttributeListShortcut implements ArrayAccess
{

    private $attributeList;

    public function __construct(AttributeListInterface $attributeList)
    {
        $this->attributeList = $attributeList;
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this
            ->attributeList
            ->getAttribute($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this
            ->attributeList
            ->setAttribute($offset, $value);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this
            ->attributeList
            ->attributeExists($offset);
    }

    /**
     * @param mixed $offset
     * @throws Exception
     */
    public function offsetUnset($offset)
    {
        throw new Exception("Cannot remove symbol attribute");
    }
}
