<?php

namespace Remorhaz\UniLex\IO;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Lexer\TokenPosition;

class StringBuffer implements CharBufferInterface
{

    private $data;

    private $length;

    private $startOffset = 0;

    private $previewOffset = 0;

    public function __construct(string $data)
    {
        $this->data = $data;
        $this->length = strlen($data);
    }

    public function isEnd(): bool
    {
        return $this->previewOffset == $this->length;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getSymbol(): int
    {
        if ($this->previewOffset == $this->length) {
            throw new Exception("No symbol to preview at index {$this->previewOffset}");
        }
        return ord($this->data{$this->previewOffset});
    }

    /**
     * @throws Exception
     */
    public function nextSymbol(): void
    {
        if ($this->previewOffset == $this->length) {
            throw new Exception("Unexpected end of buffer on preview at index {$this->previewOffset}");
        }
        $this->previewOffset++;
    }

    public function resetToken(): void
    {
        $this->previewOffset = $this->startOffset;
    }

    /**
     * @param int $offset
     * @throws Exception
     */
    public function setToken(int $offset): void
    {
        if ($offset < 0) {
            throw new Exception("Negative token offset at index {$offset}");
        }
        if ($offset >= $this->length) {
            throw new Exception("Token offset outside of buffer at index {$offset}");
        }
        $this->startOffset = $offset;
        $this->previewOffset = $offset;
    }

    public function finishToken(Token $token): void
    {
        $this->startOffset = $this->previewOffset;
    }

    /**
     * @return TokenPosition
     * @throws Exception
     */
    public function getTokenPosition(): TokenPosition
    {
        return new TokenPosition($this->startOffset, $this->previewOffset);
    }
}
