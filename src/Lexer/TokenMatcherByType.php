<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\IO\CharBufferInterface;

class TokenMatcherByType implements TokenMatcherInterface
{
    private ?Token $token = null;

    /**
     * @throws Exception
     */
    public function getToken(): Token
    {
        return $this->token ?? throw new Exception("Token is not defined");
    }

    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
    {
        unset($this->token);
        $tokenId = $buffer->getSymbol();
        $this->token = $tokenFactory->createToken($tokenId);
        $buffer->nextSymbol();

        return true;
    }
}
