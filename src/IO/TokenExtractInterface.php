<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\IO;

interface TokenExtractInterface
{
    public function getTokenAsString(): string;

    /**
     * @return list<int>
     */
    public function getTokenAsArray(): array;
}
