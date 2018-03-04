<?php

namespace Remorhaz\UniLex;

class LexemeReader implements LexemeReaderInterface
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
        $lexeme = $this->buffer->isEnd()
            ? $this->matchEoiLexeme()
            : $this->matchSymbolLexeme();
        $this->buffer->finishLexeme($lexeme);
        return $lexeme;
    }

    /**
     * @return Lexeme
     * @throws Exception
     */
    private function matchEoiLexeme(): Lexeme
    {
        if ($this->isEnd) {
            throw new Exception("Buffer end reached");
        }
        $this->isEnd = true;
        return $this->lexemeFactory->createEoiLexeme();
    }

    private function matchSymbolLexeme(): Lexeme
    {
        return $this->matcher->match($this->buffer, $this->lexemeFactory);
    }
}
