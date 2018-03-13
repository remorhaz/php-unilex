<?php

namespace Remorhaz\UniLex\Parser;

class ParseTreeSymbolNode implements ParseTreeNodeInterface
{

    private $symbol;

    private $childList = [];

    public function __construct(ParsedSymbol $symbol)
    {
        $this->symbol = $symbol;
    }

    public function getIndex(): int
    {
        return $this->symbol->getIndex();
    }

    public function getSymbol(): ParsedSymbol
    {
        return $this->symbol;
    }

    public function addChild(ParseTreeNodeInterface $node): void
    {
        $this->childList[] = $node;
    }

    /**
     * @return ParseTreeNodeInterface[]
     */
    public function getChildList(): array
    {
        return $this->childList;
    }
}
