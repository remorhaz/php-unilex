<?php

namespace Remorhaz\UniLex;

interface CharBufferInterface
{

    public function isEnd(): bool;

    public function nextSymbol(): void;

    public function resetToken(): void;

    public function finishToken(Token $token): void;

    public function getSymbol(): int;
}
