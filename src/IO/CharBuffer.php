<?php

namespace Remorhaz\UniLex\IO;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Lexer\TokenPosition;

class CharBuffer implements CharBufferInterface, TokenExtractInterface
{

    private $data;

    private $length;

    private $startOffset = 0;

    private $previewOffset = 0;

    public function __construct(int ...$data)
    {
        $this->data = $data;
        $this->length = count($data);
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
        return $this->data[$this->previewOffset];
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

    /**
     * @param int $repeat
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

    /**
     * @param Token $token
     */
    public function finishToken(Token $token): void
    {
        $this->startOffset = $this->previewOffset;
    }

    public function getTokenAsArray(): array
    {
        $result = [];
        for ($i = $this->startOffset; $i < $this->previewOffset; $i++) {
            $result[] = $this->data[$i];
        }
        return $result;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getTokenAsString(): string
    {
        $result = '';
        foreach ($this->getTokenAsArray() as $index => $char) {
            if ($char < 0 || $char > 0xFF) {
                $offset = $this->startOffset + $index;
                throw new Exception("Only 8-bit symbols can be converted to string, {$char} found at index {$offset}");
            }
            $result .= chr($char);
        }
        return $result;
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
