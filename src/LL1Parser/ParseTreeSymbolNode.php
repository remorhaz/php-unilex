<?php

namespace Remorhaz\UniLex\LL1Parser;

class ParseTreeSymbolNode implements ParseTreeNodeInterface
{

    private $symbolId;

    private $childList = [];

    public function __construct(int $symbolId)
    {
        $this->symbolId = $symbolId;
    }

    public function getSymbolId(): int
    {
        return $this->symbolId;
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
