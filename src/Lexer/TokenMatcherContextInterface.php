<?php

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\Token;

interface TokenMatcherContextInterface
{

    public function setNewToken(int $tokenType): self;

    public function getToken(): Token;
}
