<?php

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\Token;

interface TokenMatcherContextInterface
{

    public function setNewToken(int $tokenType): self;

    public function setTokenAttribute(string $name, $value): self;

    public function getToken(): Token;

    public function getBuffer(): CharBufferInterface;
}
