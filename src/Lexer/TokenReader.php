<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\Exception;

class TokenReader implements TokenReaderInterface
{
    private bool $isEnd = false;

    public function __construct(
        private CharBufferInterface $buffer,
        private TokenMatcherInterface $matcher,
        private TokenFactoryInterface $tokenFactory,
    ) {
    }

    /**
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
     * @throws Exception
     */
    private function matchEoiToken(): Token
    {
        $this->isEnd = $this->isEnd
            ? throw new Exception("Buffer end reached")
            : true;

        return $this->tokenFactory->createEoiToken();
    }

    /**
     * @throws Exception
     */
    private function matchSymbolToken(): Token
    {
        $result = $this->matcher->match($this->buffer, $this->tokenFactory);
        if ($result) {
            return $this->matcher->getToken();
        }

        $position = $this->buffer->getTokenPosition();
        throw $this->buffer->isEnd()
            ? new Exception("Unexpected end of input at position {$position->getFinishOffset()}")
            : new Exception("Unexpected character at position {$position->getFinishOffset()}");
    }
}
