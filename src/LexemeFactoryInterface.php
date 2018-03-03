<?php

namespace Remorhaz\UniLex;

interface LexemeFactoryInterface
{

    public function createLexeme(int $tokenId): Lexeme;
}