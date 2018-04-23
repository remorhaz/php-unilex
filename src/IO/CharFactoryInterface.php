<?php

namespace Remorhaz\UniLex\IO;

use Remorhaz\UniLex\Lexer\Token;

interface CharFactoryInterface
{

    public function getChar(Token $token): int;
}
