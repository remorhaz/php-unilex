<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\LexemeInfoInterface;
use Remorhaz\UniLex\LexemeMatcherInterface;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferInterface;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;

class Buffer implements SymbolBufferInterface
{

    private $parentBuffer;

    private $matcher;

    private $startPosition = 0;

    private $previewPosition = 0;

    private $previewBuffer = [];

    private $startLexemeInfo;

    private $previewLexemeInfo;

    public function __construct(SymbolBufferInterface $parentBuffer, LexemeMatcherInterface $matcher)
    {
        $this->parentBuffer = $parentBuffer;
        $this->matcher = $matcher;
    }

    public function isEnd(): bool
    {
        return $this->parentBuffer->isEnd();
    }

    /**
     * @throws Exception
     */
    public function nextSymbol(): void
    {
        if ($this->parentBuffer->isEnd()) {
            throw new Exception("Unexpected end of buffer at index {$this->previewPosition}");
        }
        $this->cachePreviewLexeme();
        $this->previewPosition++;
    }

    public function resetLexeme(): void
    {
        $this->previewPosition = $this->startPosition;
        unset($this->startLexemeInfo, $this->previewLexemeInfo);
    }

    public function finishLexeme(): void
    {
        $this->startPosition = $this->previewPosition;
        unset($this->startLexemeInfo, $this->previewLexemeInfo);
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getSymbol(): int
    {
        if ($this->parentBuffer->isEnd()) {
            throw new Exception("No symbol to preview at index {$this->previewPosition}");
        }
        $this->cachePreviewLexeme();
        return $this->previewBuffer[$this->previewPosition];
    }

    /**
     * @return LexemeInfoInterface
     * @todo Critical performance pitfall
     */
    public function getLexemeInfo(): LexemeInfoInterface
    {
        $symbols = array_slice($this->previewBuffer, $this->startPosition, $this->previewPosition, true);
        return new SymbolBufferLexemeInfo(
            SymbolBuffer::fromSymbols(...$symbols),
            $this->startPosition,
            $this->previewPosition,
            $this->parentBuffer->getLexemeInfo()
        );
    }

    /**
     * @throws Exception
     */
    private function cachePreviewLexeme(): void
    {
        if (isset($this->previewBuffer[$this->previewPosition])) {
            return;
        }
        $lexeme = $this->matcher->match($this->parentBuffer);
        if (!($lexeme instanceof SymbolLexeme)) {
            throw new Exception("Invalid lexeme at index {$this->previewPosition}");
        }
        $this->previewBuffer[$this->previewPosition] = $lexeme->getSymbol();
        if (!isset($this->startLexemeInfo)) {
            $this->startLexemeInfo = $lexeme->getInfo();
        }
        if (!isset($this->previewLexemeInfo)) {
            $this->previewLexemeInfo = $lexeme->getInfo();
        }
    }
}
