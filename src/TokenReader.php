<?php

namespace Remorhaz\UniLex;

class TokenReader implements TokenReaderInterface
{

    private $buffer;

    private $matcher;

    private $isEnd = false;

    private $tokenFactory;

    public function __construct(
        SymbolBufferInterface $buffer,
        TokenMatcherInterface $matcher,
        TokenFactoryInterface $tokenFactory
    ) {
        $this->buffer = $buffer;
        $this->matcher = $matcher;
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * @return Token
     * @throws Exception
     */
    public function read(): Token
    {
        $token = $this->buffer->isEnd()
            ? $this->matchEoiToken()
            : $this->matchSymbolToken();
        $this->buffer->finishToken($token);
        return $token;
    }

    /**
     * @return Token
     * @throws Exception
     */
    private function matchEoiToken(): Token
    {
        if ($this->isEnd) {
            throw new Exception("Buffer end reached");
        }
        $this->isEnd = true;
        return $this->tokenFactory->createEoiToken();
    }

    private function matchSymbolToken(): Token
    {
        return $this->matcher->match($this->buffer, $this->tokenFactory);
    }
}
