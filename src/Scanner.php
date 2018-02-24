<?php

namespace Remorhaz\UniLex;

use Remorhaz\UniLex\LexemeMatcherInterface;

class Scanner
{

    private $buffer;

    private $matcher;

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
        $lexeme = $this->matcher->match($this->buffer);
        $this->buffer->finishLexeme();
        return $lexeme;
    }
}
