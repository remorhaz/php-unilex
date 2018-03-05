<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

class CharBuffer implements CharBufferInterface, TokenExtractInterface
{

    /**
     * @var SplFixedArray
     */
    private $data;

    private $length;

    private $startOffset = 0;

    private $previewOffset = 0;

    public function __construct(SplFixedArray $data)
    {
        $this->data = $data;
        $this->length = $data->count();
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
        $token->setBufferInfo($this->getTokenInfo());
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

    /**
     * @return TokenBufferInfoInterface
     * @throws Exception
     */
    private function getTokenInfo(): TokenBufferInfoInterface
    {
        $position = new TokenPosition($this->startOffset, $this->previewOffset);
        return new TokenBufferInfo($this, $position);
    }
}
