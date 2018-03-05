<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

class CharBuffer implements CharBufferInterface, TokenExtractInterface
{

    private const DEFAULT_TOKEN_ATTRIBUTE_PREFIX = 'buffer';

    /**
     * @var SplFixedArray
     */
    private $data;

    private $length;

    private $startOffset = 0;

    private $previewOffset = 0;

    private $tokenAttributePrefix;

    public function __construct(SplFixedArray $data, $tokenAttributePrefix = self::DEFAULT_TOKEN_ATTRIBUTE_PREFIX)
    {
        $this->data = $data;
        $this->length = $data->count();
        $this->tokenAttributePrefix = $tokenAttributePrefix;
    }

    public static function fromString(string $text): self
    {
        $length = strlen($text);
        $data = new SplFixedArray($length);
        for ($i = 0; $i < $length; $i++) {
            $data->offsetSet($i, ord($text[$i]));
        }
        return new self($data);
    }

    public static function fromSymbols(int ...$array): self
    {

        $data = SplFixedArray::fromArray($array);
        return new self($data);
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
        return $this->data->offsetGet($this->previewOffset);
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
     * @param Token $token
     * @throws Exception
     */
    public function finishToken(Token $token): void
    {
        $token->setAttribute("{$this->tokenAttributePrefix}.position.start", $this->startOffset);
        $token->setAttribute("{$this->tokenAttributePrefix}.position.finish", $this->previewOffset);
        $this->startOffset = $this->previewOffset;
    }

    public function extractToken(TokenPosition $position): SplFixedArray
    {
        $startOffset = $position->getStartOffset();
        $tokenLength = $position->getLength();
        $output = new SplFixedArray($tokenLength);
        for ($i = 0; $i < $tokenLength; $i++) {
            $symbol = $this->data->offsetGet($startOffset + $i);
            $output->offsetSet($i, $symbol);
        }
        return $output;
    }
}
