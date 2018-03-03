<?php

namespace Remorhaz\UniLex;

use Remorhaz\UniLex\Grammar\ContextFreeGrammarInterface;

class LexemeFactory implements LexemeFactoryInterface
{

    private $grammar;

    public function __construct(ContextFreeGrammarInterface $grammar)
    {
        $this->grammar = $grammar;
    }

    public function createLexeme(int $tokenId): Lexeme
    {
        return new Lexeme($tokenId, $this->isEoi($tokenId));
    }

    protected function isEoi(int $tokenId): bool
    {
        return $this->grammar->isEoiToken($tokenId);
    }
}
