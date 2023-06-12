<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\IO\CharBufferInterface;

interface TokenMatcherContextInterface
{
    public function setNewToken(int $tokenType): self;

    public function setTokenAttribute(string $name, mixed $value): self;

    public function getToken(): Token;

    public function getBuffer(): CharBufferInterface;

    public function getSymbolString(): string;

    /**
     * @return list<int>
     */
    public function getSymbolList(): array;

    public function getMode(): string;

    public function setMode(string $mode): self;
}
