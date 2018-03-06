<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\CharFactoryInterface;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;

class CharFactory implements CharFactoryInterface
{

    public function __construct()
    {
    }

    /**
     * @param Token $token
     * @return int
     * @throws Exception
     */
    public function getChar(Token $token): int
    {
        return $token->getAttribute(TokenAttribute::UNICODE_CHAR);
    }
}
