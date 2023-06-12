<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Grammar\ContextFree;

use Stringable;

class Production implements Stringable
{
    /**
     * @var list<int>
     */
    private array $symbolList;

    public function __construct(
        private int $headerId,
        private int $index,
        int ...$symbolList,
    ) {
        $this->symbolList = $symbolList;
    }

    public function __toString()
    {
        return "$this->headerId:$this->index";
    }

    public function getHeaderId(): int
    {
        return $this->headerId;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return list<int>
     */
    public function getSymbolList(): array
    {
        return $this->symbolList;
    }

    public function isEpsilon(): bool
    {
        return empty($this->symbolList);
    }
}
