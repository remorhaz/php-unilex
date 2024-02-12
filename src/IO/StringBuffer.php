<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\IO;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Lexer\TokenPosition;

class StringBuffer implements CharBufferInterface, TokenExtractInterface
{
    private int $length;

    private int $startOffset = 0;

    private int $previewOffset = 0;

    public function __construct(
        private readonly string $data,
    ) {
        $this->length = strlen($data);
    }

    public function isEnd(): bool
    {
        return $this->previewOffset == $this->length;
    }

    /**
     * @throws Exception
     */
    public function getSymbol(): int
    {
        return $this->previewOffset == $this->length
            ? throw new Exception("No symbol to preview at index {$this->previewOffset}")
            : ord($this->data[$this->previewOffset]);
    }

    /**
     * @throws Exception
     */
    public function nextSymbol(): void
    {
        $this->previewOffset == $this->length
            ? throw new Exception("Unexpected end of buffer on preview at index {$this->previewOffset}")
            : $this->previewOffset++;
    }

    /**
     * @throws Exception
     */
    public function prevSymbol(int $repeat = 1): void
    {
        if ($repeat < 1) {
            throw new Exception("Non-positive unread repeat counter: {$repeat}");
        }

        if ($this->previewOffset - $repeat < $this->startOffset) {
            throw new Exception("Invalid unread repeat counter: {$repeat}");
        }

        $this->previewOffset -= $repeat;
    }

    public function resetToken(): void
    {
        $this->previewOffset = $this->startOffset;
    }

    public function finishToken(Token $token): void
    {
        $this->startOffset = $this->previewOffset;
    }

    /**
     * @throws Exception
     */
    public function getTokenPosition(): TokenPosition
    {
        return new TokenPosition($this->startOffset, $this->previewOffset);
    }

    public function getTokenAsString(): string
    {
        return substr($this->data, $this->startOffset, $this->previewOffset - $this->startOffset);
    }

    /**
     * @return list<int>
     */
    public function getTokenAsArray(): array
    {
        $result = [];
        for ($i = $this->startOffset; $i < $this->previewOffset; $i++) {
            $result[] = ord($this->data[$i]);
        }

        return $result;
    }
}
