<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\CharFactoryInterface;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;

class CharFactory implements CharFactoryInterface
{

    private $unicodeCharAttribute;

    public function __construct(string $unicodeCharAttribute = TokenAttribute::UNICODE_CHAR)
    {
        $this->unicodeCharAttribute = $unicodeCharAttribute;
    }

    /**
     * @param Token $token
     * @return int
     * @throws Exception
     */
    public function getChar(Token $token): int
    {
        return $token->getAttribute($this->unicodeCharAttribute);
    }
}
