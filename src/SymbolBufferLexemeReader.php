<?php

namespace Remorhaz\UniLex;

class SymbolBufferLexemeReader implements LexemeReaderInterface
{

    private $buffer;

    private $matcher;

    private $isEnd = false;

    private $lexemeFactory;

    public function __construct(
        SymbolBufferInterface $buffer,
        LexemeMatcherInterface $matcher,
        LexemeFactoryInterface $lexemeFactory
    ) {
        $this->buffer = $buffer;
        $this->matcher = $matcher;
        $this->lexemeFactory = $lexemeFactory;
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
        $lexeme = $this->lexemeFactory->createEoiLexeme();
        $this->buffer->finishLexeme();
        return $lexeme;
    }

    private function readSymbolLexeme(): Lexeme
    {
        $lexeme = $this->matcher->match($this->buffer, $this->lexemeFactory);
        $this->buffer->finishLexeme();
        return $lexeme;
    }
}
