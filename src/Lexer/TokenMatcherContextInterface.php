<?php

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\IO\CharBufferInterface;

interface TokenMatcherContextInterface
{

    public function setNewToken(int $tokenType): self;

    public function setTokenAttribute(string $name, $value): self;

    public function getToken(): Token;

    public function getBuffer(): CharBufferInterface;

    public function getSymbolString(): string;

    public function getSymbolList(): array;

    public function getMode(): string;

    public function setMode(string $mode): self;

    public function setRegExps(string $mode, string ...$regExps): void;

    public function allowRegExps(string ...$regExps): void;

    public function getRegExp(): string;

    public function visitTransition(string $hash): void;

    public function isVisitedTransition(string ...$hashes): bool;
}
