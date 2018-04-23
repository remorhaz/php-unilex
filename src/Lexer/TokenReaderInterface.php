<?php

namespace Remorhaz\UniLex\Lexer;

interface TokenReaderInterface
{

    public function read(): Token;
}
