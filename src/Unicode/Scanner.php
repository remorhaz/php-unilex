<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\Unicode\ScannerLexemeListener;
use Remorhaz\UniLex\SymbolBufferInterface;

class Scanner
{

    private $buffer;

    private $matcher;

    private $lexemeListener;

    private $isEnd = false;

    public function __construct(SymbolBufferInterface $buffer, LexemeMatcherInterface $matcher)
    {
        $this->buffer = $buffer;
        $this->matcher = $matcher;
    }

    /**
     * @return Lexeme
     * @throws Exception
     */
    public function read(): Lexeme
    {
        return $this->buffer->isLexemeEnd()
            ? $this->readEofLexeme()
            : $this->readSymbolLexeme();
    }

    private function getLexemeListener(): ScannerLexemeListener
    {
        if (!isset($this->lexemeListener)) {
            $this->lexemeListener = new ScannerLexemeListener;
        }
        return $this->lexemeListener;
    }

    /**
     * @return EofLexeme
     * @throws Exception
     */
    private function readEofLexeme(): EofLexeme
    {
        if ($this->isEnd) {
            throw new Exception("Buffer end reached");
        }
        $this->isEnd = true;
        return new EofLexeme($this->buffer->getLexemeInfo());
    }

    private function readSymbolLexeme(): Lexeme
    {
        $lexemeListener = $this->getLexemeListener();
        $lexemeListener->resetLexeme();
        $this->matcher->match($this->buffer, $lexemeListener);
        $this->buffer->finishLexeme();
        return $lexemeListener->getLexeme();
    }
}
