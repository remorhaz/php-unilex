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

    public function resetLexeme(): void
    {
        $this->previewOffset = $this->startOffset;
    }

    /**
     * @param Lexeme $lexeme
     * @throws Exception
     */
    public function finishLexeme(Lexeme $lexeme): void
    {
        $lexeme->setBufferInfo($this->getLexemeInfo());
        $this->startOffset = $this->previewOffset;
    }

    public function extractLexeme(LexemePosition $position): SplFixedArray
    {
        $startOffset = $position->getStartOffset();
        $lexemeLength = $position->getLength();
        $output = new SplFixedArray($lexemeLength);
        for ($i = 0; $i < $lexemeLength; $i++) {
            $symbol = $this->data->offsetGet($startOffset + $i);
            $output->offsetSet($i, $symbol);
        }
        return $output;
    }

    /**
     * @return BufferInfoInterface
     * @throws Exception
     */
    private function getLexemeInfo(): BufferInfoInterface
    {
        $position = new LexemePosition($this->startOffset, $this->previewOffset);
        return new SymbolBufferLexemeInfo($this, $position);
    }
}
