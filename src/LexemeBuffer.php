<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

class LexemeBuffer implements SymbolBufferInterface, LexemeExtractInterface
{

    private $reader;

    private $symbolFactory;

    /**
     * @var Lexeme[]
     */
    private $data = [];

    private $startOffset = 0;

    private $previewOffset = 0;

    public function __construct(LexemeReaderInterface $reader, SymbolFactoryInterface $symbolFactory)
    {
        $this->reader = $reader;
        $this->symbolFactory = $symbolFactory;
    }

    public function isEnd(): bool
    {
        return $this->getLexeme()->isEoi();
    }

    /**
     * @throws Exception
     */
    public function nextSymbol(): void
    {
        if ($this->isEnd()) {
            throw new Exception("Unexpected end of buffer at index {$this->previewOffset}");
        }
        $this->cacheLexeme();
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

    /**
     * @return int
     * @throws Exception
     */
    public function getSymbol(): int
    {
        if ($this->isEnd()) {
            throw new Exception("No symbol to preview at index {$this->previewOffset}");
        }
        return $this->symbolFactory->getSymbol($this->getLexeme());
    }

    /**
     * @return BufferInfoInterface
     * @throws Exception
     * @todo Attach merged source input info, maybe?
     */
    private function getLexemeInfo(): BufferInfoInterface
    {
        $position = new LexemePosition($this->startOffset, $this->previewOffset);
        return new SymbolBufferLexemeInfo($this, $position);
    }

    public function extractLexeme(LexemePosition $position): SplFixedArray
    {
        $startOffset = $position->getStartOffset();
        $lexemeLength = $position->getLength();
        $output = new SplFixedArray($lexemeLength);
        for ($i = 0; $i < $lexemeLength; $i++) {
            $lexeme = $this->data[$startOffset + $i];
            $symbol = $this->symbolFactory->getSymbol($lexeme);
            $output->offsetSet($i, $symbol);
        }
        return $output;
    }

    private function getLexeme(): Lexeme
    {
        $this->cacheLexeme();
        return $this->data[$this->previewOffset];
    }

    private function cacheLexeme(): void
    {
        if (!isset($this->data[$this->previewOffset])) {
            $this->data[$this->previewOffset] = $this->reader->read();
        }
    }
}
