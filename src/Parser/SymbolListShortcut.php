<?php

namespace Remorhaz\UniLex\Parser;

use ArrayAccess;
use Remorhaz\UniLex\Exception;

class SymbolListShortcut implements ArrayAccess
{

    private $production;

    public function __construct(Production $production)
    {
        $this->production = $production;
    }

    /**
     * @param mixed $offset
     * @return mixed|array|AttributeListShortcut
     * @throws Exception
     */
    public function offsetGet($offset)
    {
        $symbol = $this
            ->production
            ->getSymbol($offset);
        return new AttributeListShortcut($symbol);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception("Cannot change production symbol");
    }

    /**
     * @param mixed $offset
     * @throws Exception
     */
    public function offsetUnset($offset)
    {
        throw new Exception("Cannot remove production symbol");
    }

    public function offsetExists($offset)
    {
        return $this
            ->production
            ->symbolExists($offset);
    }
}
