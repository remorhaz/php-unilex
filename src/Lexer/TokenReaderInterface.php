<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

interface TokenReaderInterface
{
    public function read(): Token;
}
