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
            ? $this->readEofLexeme()
            : $this->readSymbolLexeme();
    }

    /**
     * @return EoiLexeme
     * @throws Exception
     */
    private function readEofLexeme(): EoiLexeme
    {
        if ($this->isEnd) {
            throw new Exception("Buffer end reached");
        }
        $this->isEnd = true;
        return new EoiLexeme($this->buffer->getLexemeInfo(), $this->eoiLexemeType);
    }

    private function readSymbolLexeme(): Lexeme
    {
        $lexeme = $this->matcher->match($this->buffer);
        $this->buffer->finishLexeme();
        return $lexeme;
    }
}
