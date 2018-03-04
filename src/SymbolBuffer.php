<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

class SymbolBuffer implements SymbolBufferInterface, LexemeExtractInterface
{

    /**
     * @var SplFixedArray
     */
    private $data;

    private $length;

    private $startPosition = 0;

    private $previewPosition = 0;

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
        return $this->previewPosition == $this->length;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getSymbol(): int
    {
        if ($this->previewPosition == $this->length) {
            throw new Exception("No symbol to preview at index {$this->previewPosition}");
        }
        return $this->data->offsetGet($this->previewPosition);
    }

    /**
     * @throws Exception
     */
    public function nextSymbol(): void
    {
        if ($this->previewPosition == $this->length) {
            throw new Exception("Unexpected end of buffer on preview at index {$this->previewPosition}");
        }
        $this->previewPosition++;
    }

    public function resetLexeme(): void
    {
        $this->previewPosition = $this->startPosition;
    }

    public function finishLexeme(Lexeme $lexeme): void
    {
        $this->startPosition = $this->previewPosition;
    }

    public function extractLexeme(LexemePosition $position): SplFixedArray
    {
        $startOffset = $position->getStartOffset();
        $lexemeLength = $position->getLength();
        $lexeme = new SplFixedArray($lexemeLength);
        for ($i = 0; $i < $lexemeLength; $i++) {
            $symbol = $this->data->offsetGet($startOffset + $i);
            $lexeme->offsetSet($i, $symbol);
        }
        return $lexeme;
    }

    /**
     * @return LexemeInfoInterface
     * @throws Exception
     */
    public function getLexemeInfo(): LexemeInfoInterface
    {
        $position = new LexemePosition($this->startPosition, $this->previewPosition);
        return new SymbolBufferLexemeInfo($this, $position);
    }
}
