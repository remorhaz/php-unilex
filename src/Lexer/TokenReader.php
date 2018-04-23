<?php

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\Exception;

class TokenReader implements TokenReaderInterface
{

    private $buffer;

    private $matcher;

    private $isEnd = false;

    private $tokenFactory;

    public function __construct(
        CharBufferInterface $buffer,
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
        $this->matcher->match($this->buffer, $this->tokenFactory);
        return $this->matcher->getToken();
    }
}
