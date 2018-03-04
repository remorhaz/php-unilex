<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeExtractInterface;
use Remorhaz\UniLex\LexemeFactoryInterface;
use Remorhaz\UniLex\LexemeInfoInterface;
use Remorhaz\UniLex\LexemeMatcherInterface;
use Remorhaz\UniLex\LexemePosition;
use Remorhaz\UniLex\SymbolBufferInterface;
use SplFixedArray;

class SymbolBuffer implements SymbolBufferInterface, LexemeExtractInterface
{

    private $source;

    private $matcher;

    private $startOffset = 0;

    private $previewOffset = 0;

    private $data = [];

    private $sourceStartOffset = 0;

    private $sourcePreviewOffset = 0;

    private $lexemeFactory;

    public function __construct(
        SymbolBufferInterface $source,
        LexemeMatcherInterface $matcher,
        LexemeFactoryInterface $lexemeFactory
    ) {
        $this->source = $source;
        $this->matcher = $matcher;
        $this->lexemeFactory = $lexemeFactory;
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
            throw new Exception("Unexpected end of buffer at index {$this->previewOffset}");
        }
        $this->cachePreviewLexeme();
        $this->previewOffset++;
        $this->sourcePreviewOffset = $this
            ->source
            ->getLexemeInfo()
            ->getPosition()
            ->getFinishOffset();
    }

    public function resetLexeme(): void
    {
        $this->previewOffset = $this->startOffset;
        $this->sourcePreviewOffset = $this->sourceStartOffset;
    }

    public function finishLexeme(Lexeme $lexeme): void
    {
        $this->startOffset = $this->previewOffset;
        $this->sourceStartOffset = $this->sourcePreviewOffset;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getSymbol(): int
    {
        if ($this->source->isEnd()) {
            throw new Exception("No symbol to preview at index {$this->previewOffset}");
        }
        $this->cachePreviewLexeme();
        return $this->data[$this->previewOffset];
    }

    /**
     * @return LexemeInfoInterface
     * @throws Exception
     */
    public function getLexemeInfo(): LexemeInfoInterface
    {
        $position = new LexemePosition($this->startOffset, $this->previewOffset);
        $sourcePosition = new LexemePosition($this->sourceStartOffset, $this->sourcePreviewOffset);
        return new LexemeInfo($this, $position, $sourcePosition);
    }

    /**
     * @param LexemePosition $position
     * @return SplFixedArray
     * @throws Exception
     */
    public function extractLexeme(LexemePosition $position): SplFixedArray
    {
        $startOffset = $position->getStartOffset();
        $length = $position->getLength();
        if (0 == $length && $startOffset > $this->previewOffset) {
            throw new Exception("No symbol to extract at offset {$startOffset}");
        }
        $lexeme = new SplFixedArray($length);
        for ($i = 0; $i < $length; $i++) {
            $offset = $startOffset + $i;
            if (!isset($this->data[$offset])) {
                throw new Exception("No symbol to extract at offset {$offset}");
            }
            $symbol = $this->data[$offset];
            $lexeme->offsetSet($i, $symbol);
        }
        return $lexeme;
    }

    /**
     * @throws Exception
     */
    private function cachePreviewLexeme(): void
    {
        if (isset($this->data[$this->previewOffset])) {
            return;
        }
        $lexeme = $this->matcher->match($this->source, $this->lexemeFactory);
        $symbolInfo = $lexeme->getMatcherInfo();
        if (!isset($symbolInfo)) {
            throw new Exception("No matcher info in lexeme at index {$this->previewOffset}");
        }
        if (!($symbolInfo instanceof SymbolInfo)) {
            throw new Exception("Invalid lexeme info at index {$this->previewOffset}");
        }
        $this->data[$this->previewOffset] = $symbolInfo->getCode();
    }
}
