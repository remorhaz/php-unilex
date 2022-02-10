<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\IO;

interface TokenExtractInterface
{
    public function getTokenAsString(): string;

    public function getTokenAsArray(): array;
}
