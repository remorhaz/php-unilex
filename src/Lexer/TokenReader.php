<?php

declare(strict_types=1);

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

    /**
     * @return Token
     * @throws Exception
     */
    private function matchSymbolToken(): Token
    {
        $result = $this->matcher->match($this->buffer, $this->tokenFactory);
        if ($result) {
            return $this->matcher->getToken();
        }
        $position = $this->buffer->getTokenPosition();
        if ($this->buffer->isEnd()) {
            throw new Exception("Unexpected end of input at position {$position->getFinishOffset()}");
        }
        throw new Exception("Unexpected character at position {$position->getFinishOffset()}");
    }
}
