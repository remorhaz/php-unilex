<?php

namespace Remorhaz\UniLex\IO;

interface TokenExtractInterface
{

    public function getTokenAsString(): string;

    public function getTokenAsArray(): array;
}
