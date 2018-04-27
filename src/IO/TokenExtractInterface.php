<?php

namespace Remorhaz\UniLex\IO;

interface TokenExtractInterface
{

    public function asString(): string;

    public function asArray(): array;
}
