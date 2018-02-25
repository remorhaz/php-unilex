<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\LexemeInfoInterface;
use Remorhaz\UniLex\LexemePosition;
use Remorhaz\UniLex\SymbolBuffer as ByteSymbolBuffer;
use Remorhaz\UniLex\SymbolBufferInterface;

class SymbolBuffer implements SymbolBufferInterface
{

    private $source;

    private $matcher;

    private $startPosition = 0;

    private $previewPosition = 0;

    private $data;

    private $sourceStartPosition = 0;

    private $sourcePreviewPosition = 0;

    public function __construct(SymbolBufferInterface $source, LexemeMatcherInterface $matcher)
    {
        $this->source = $source;
        $this->matcher = $matcher;
        $this->data = new \SplFixedArray;
    }

    public function isEnd(): bool
    {
        return $this->source->isEnd();
    }

    /**
     * @throws Exception
     */
    public function nextSymbol(): void
    {
        if ($this->source->isEnd()) {
            throw new Exception("Unexpected end of buffer at index {$this->previewPosition}");
        }
        $this->cachePreviewLexeme();
        $this->previewPosition++;
        $this->sourcePreviewPosition = $this
            ->source
            ->getLexemeInfo()
            ->getPosition()
            ->getFinishOffset();
    }

    public function resetLexeme(): void
    {
        $this->previewPosition = $this->startPosition;
        $this->sourcePreviewPosition = $this->sourceStartPosition;
    }

    public function finishLexeme(): void
    {
        $this->startPosition = $this->previewPosition;
        $this->sourceStartPosition = $this->sourcePreviewPosition;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getSymbol(): int
    {
        if ($this->source->isEnd()) {
            throw new Exception("No symbol to preview at index {$this->previewPosition}");
        }
        $this->cachePreviewLexeme();
        return $this->data->offsetGet($this->previewPosition);
    }

    /**
     * @return LexemeInfoInterface
     * @todo Critical performance pitfall
     */
    public function getLexemeInfo(): LexemeInfoInterface
    {
        return new LexemeInfo(
            new ByteSymbolBuffer($this->data), // @todo Get rid of this ugly thing
            new LexemePosition($this->startPosition, $this->previewPosition),
            new LexemePosition($this->sourceStartPosition, $this->sourcePreviewPosition)
        );
    }

    /**
     * @throws Exception
     */
    private function cachePreviewLexeme(): void
    {
        if ($this->data->offsetExists($this->previewPosition)) {
            return;
        }
        $lexeme = $this->matcher->match($this->source);
        if (!($lexeme instanceof SymbolLexeme)) {
            throw new Exception("Invalid lexeme at index {$this->previewPosition}");
        }
        $this->data->offsetSet($this->previewPosition, $lexeme->getSymbol());
    }
}
