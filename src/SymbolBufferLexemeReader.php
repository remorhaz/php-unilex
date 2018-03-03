<?php

namespace Remorhaz\UniLex;

class SymbolBufferLexemeReader implements LexemeReaderInterface
{

    private $buffer;

    private $matcher;

    private $isEnd = false;

    private $eoiLexemeType;

    public function __construct(SymbolBufferInterface $buffer, LexemeMatcherInterface $matcher, int $eoiLexemeType)
    {
        $this->buffer = $buffer;
        $this->matcher = $matcher;
        $this->eoiLexemeType = $eoiLexemeType;
    }

    /**
     * @return Lexeme
     * @throws Exception
     */
    public function read(): Lexeme
    {
        return $this->buffer->isEnd()
            ? $this->readEoiLexeme()
            : $this->readSymbolLexeme();
    }

    /**
     * @return Lexeme
     * @throws Exception
     */
    private function readEoiLexeme(): Lexeme
    {
        if ($this->isEnd) {
            throw new Exception("Buffer end reached");
        }
        $this->isEnd = true;
        return new Lexeme($this->eoiLexemeType, true);
    }

    private function readSymbolLexeme(): Lexeme
    {
        $lexeme = $this->matcher->match($this->buffer);
        $this->buffer->finishLexeme();
        return $lexeme;
    }
}
