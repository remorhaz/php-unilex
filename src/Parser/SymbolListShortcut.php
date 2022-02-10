<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser;

use ArrayAccess;
use Remorhaz\UniLex\Exception;
use ReturnTypeWillChange;

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
    #[ReturnTypeWillChange]
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
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new Exception("Cannot change production symbol");
    }

    /**
     * @param mixed $offset
     * @throws Exception
     */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new Exception("Cannot remove production symbol");
    }

    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this
            ->production
            ->symbolExists($offset);
    }
}
