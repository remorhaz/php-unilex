<?php

namespace Remorhaz\UniLex;

interface SymbolBufferInterface
{

    public function isEnd(): bool;

    public function nextSymbol(): void;

    public function resetLexeme(): void;

    public function finishLexeme(): void;

    public function getSymbol(): int;

    public function getLexemeInfo(): LexemeInfoInterface;
}
