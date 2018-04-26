<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Lexer\TokenFactoryInterface;
use Remorhaz\UniLex\Lexer\TokenMatcherInterface;
use Remorhaz\UniLex\Lexer\TokenPosition;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;
use Remorhaz\UniLex\Unicode\Grammar\TokenFactory;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;
use Remorhaz\UniLex\Unicode\Grammar\Utf8TokenMatcher;

class CharBuffer implements CharBufferInterface
{

    private $source;

    private $matcher;

    private $symbol;

    private $charTokenFactory;

    private $startOffset = 0;

    private $previewOffset = 0;

    public function __construct(CharBufferInterface $source)
    {
        $this->source = $source;
    }

    public function setMatcher(TokenMatcherInterface $matcher): void
    {
        $this->matcher = $matcher;
    }

    public function isEnd(): bool
    {
        return $this->source->isEnd();
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getSymbol(): int
    {
        if (!isset($this->symbol)) {
            $this->symbol = $this->matchChar();
        }
        return $this->symbol;
    }

    /**
     * @return int
     * @throws Exception
     */
    private function matchChar(): int
    {
        $result = $this
            ->getMatcher()
            ->match($this->source, $this->getCharTokenFactory());
        if (!$result) {
            throw new Exception("Failed to match Unicode char from source buffer");
        }
        $token = $this
            ->getMatcher()
            ->getToken();
        if ($token->getType() != TokenType::SYMBOL) {
            throw new Exception("Invalid Unicode char in source buffer");
        }
        return $token->getAttribute(TokenAttribute::UNICODE_CHAR);
    }

    /**
     * @throws Exception
     */
    public function nextSymbol(): void
    {
        if (!isset($this->symbol)) {
            if ($this->isEnd()) {
                throw new Exception("Unexpected end of buffer on preview at index {$this->previewOffset}");
            }
            $this->matchChar();
        }
        unset($this->symbol);
        $this->previewOffset++;
    }

    /**
     * @param Token $token
     * @throws Exception
     */
    public function finishToken(Token $token): void
    {
        $sourcePosition = $this->source->getTokenPosition();
        $token->setAttribute(TokenAttribute::UNICODE_BYTE_OFFSET_START, $sourcePosition->getStartOffset());
        $token->setAttribute(TokenAttribute::UNICODE_BYTE_OFFSET_FINISH, $sourcePosition->getFinishOffset());
        $this->source->finishToken($token);
        $token->setAttribute(TokenAttribute::UNICODE_CHAR_OFFSET_START, $this->startOffset);
        $token->setAttribute(TokenAttribute::UNICODE_CHAR_OFFSET_FINISH, $this->previewOffset);
        $this->startOffset = $this->previewOffset;
    }

    public function resetToken(): void
    {
        $this->previewOffset = $this->startOffset;
        $this->source->resetToken();
        unset($this->symbol);
    }

    /**
     * @return TokenPosition
     * @throws Exception
     */
    public function getTokenPosition(): TokenPosition
    {
        return new TokenPosition($this->startOffset, $this->previewOffset);
    }

    private function getMatcher(): TokenMatcherInterface
    {
        if (!isset($this->matcher)) {
            $this->matcher = new Utf8TokenMatcher;
        }
        return $this->matcher;
    }

    private function getCharTokenFactory(): TokenFactoryInterface
    {
        if (!isset($this->charTokenFactory)) {
            $this->charTokenFactory = new TokenFactory;
        }
        return $this->charTokenFactory;
    }
}
