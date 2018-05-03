<?php

namespace Remorhaz\UniLex\IO;

use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Lexer\TokenPosition;

interface CharBufferInterface
{

    public function isEnd(): bool;

    public function nextSymbol(): void;

    public function prevSymbol(int $repeat = 1): void;

    public function resetToken(): void;

    public function finishToken(Token $token): void;

    public function getSymbol(): int;

    public function getTokenPosition(): TokenPosition;
}
