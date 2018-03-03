<?php

namespace Remorhaz\UniLex\Grammar\ContextFree;

use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeFactoryInterface;

class LexemeFactory implements LexemeFactoryInterface
{

    private $grammar;

    public function __construct(GrammarInterface $grammar)
    {
        $this->grammar = $grammar;
    }

    public function createLexeme(int $tokenId): Lexeme
    {
        return new Lexeme($tokenId, $this->isEoi($tokenId));
    }

    public function createEoiLexeme(): Lexeme
    {
        return $this->createLexeme($this->grammar->getEoiToken());
    }

    protected function isEoi(int $tokenId): bool
    {
        return $this->grammar->isEoiToken($tokenId);
    }
}
