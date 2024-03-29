<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\IO\TokenExtractInterface;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Lexer\TokenFactoryInterface;
use Remorhaz\UniLex\Lexer\TokenMatcherInterface;
use Remorhaz\UniLex\Lexer\TokenPosition;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;
use Remorhaz\UniLex\Unicode\Grammar\TokenFactory;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;
use Remorhaz\UniLex\Unicode\Grammar\Utf8TokenMatcher;

class CharBuffer implements CharBufferInterface, TokenExtractInterface
{
    private ?TokenMatcherInterface $matcher = null;

    private ?int $char = null;

    private ?TokenFactoryInterface $tokenFactory = null;

    private int $startOffset = 0;

    private int $previewOffset = 0;

    /**
     * @var list<int>
     */
    private array $buffer = [];

    private int $sourcePreviewOffset = 0;

    public function __construct(
        private readonly CharBufferInterface $source,
    ) {
    }

    public function setMatcher(TokenMatcherInterface $matcher): void
    {
        $this->matcher = $matcher;
    }

    public function setTokenFactory(TokenFactoryInterface $tokenFactory): void
    {
        $this->tokenFactory = $tokenFactory;
    }

    public function isEnd(): bool
    {
        return $this->source->isEnd();
    }

    /**
     * @throws Exception
     */
    public function getSymbol(): int
    {
        return $this->char ??= $this->getMatchedChar();
    }

    /**
     * @return int
     * @throws Exception
     */
    private function getMatchedChar(): int
    {
        if ($this->source->isEnd()) {
            throw new Exception("Unexpected end of source buffer on preview at index {$this->previewOffset}");
        }
        $positionBeforeMatch = $this->source->getTokenPosition();
        $result = $this
            ->getMatcher()
            ->match($this->source, $this->getTokenFactory());
        if (!$result) {
            throw new Exception("Failed to match Unicode char from source buffer");
        }
        $token = $this
            ->getMatcher()
            ->getToken();
        if ($token->getType() != TokenType::SYMBOL) {
            throw new Exception("Invalid Unicode char token");
        }
        $positionAfterMatch = $this->source->getTokenPosition();
        $this->sourcePreviewOffset = $positionAfterMatch->getFinishOffset() - $positionBeforeMatch->getFinishOffset();

        return $token->getAttribute(TokenAttribute::UNICODE_CHAR);
    }

    /**
     * @throws Exception
     */
    public function nextSymbol(): void
    {
        $this->buffer[] = $this->char ?? $this->getMatchedChar();
        $this->sourcePreviewOffset = 0;
        unset($this->char);
        $this->previewOffset++;
    }

    /**
     * @param int $repeat
     * @throws Exception
     */
    public function prevSymbol(int $repeat = 1): void
    {
        throw new Exception("Unread operation is not supported");
    }

    /**
     * @param Token $token
     * @throws Exception
     */
    public function finishToken(Token $token): void
    {
        $this->cleanupPreview();
        $sourcePosition = $this->source->getTokenPosition();
        $token->setAttribute(TokenAttribute::UNICODE_BYTE_OFFSET, $sourcePosition->getStartOffset());
        $token->setAttribute(TokenAttribute::UNICODE_BYTE_LENGTH, $sourcePosition->getLength());
        $this->source->finishToken($token);
        $charLength = $this->previewOffset - $this->startOffset;
        $token->setAttribute(TokenAttribute::UNICODE_CHAR_OFFSET, $this->startOffset);
        $token->setAttribute(TokenAttribute::UNICODE_CHAR_LENGTH, $charLength);
        $this->startOffset = $this->previewOffset;
        $this->buffer = [];
    }

    public function resetToken(): void
    {
        $this->previewOffset = $this->startOffset;
        $this->source->resetToken();
        $this->buffer = [];
        $this->sourcePreviewOffset = 0;
        unset($this->char);
    }

    /**
     * @throws Exception
     */
    public function getTokenPosition(): TokenPosition
    {
        return new TokenPosition($this->startOffset, $this->previewOffset);
    }

    /**
     * @throws Exception
     */
    public function getTokenAsString(): string
    {
        if ($this->source instanceof TokenExtractInterface) {
            $this->cleanupPreview();

            return $this->source->getTokenAsString();
        }
        throw new Exception("Source buffer doesn't support extracting strings");
    }

    private function cleanupPreview(): void
    {
        if ($this->sourcePreviewOffset == 0) {
            return;
        }

        $this->source->prevSymbol($this->sourcePreviewOffset);
        $this->sourcePreviewOffset = 0;
        unset($this->char);
    }

    /**
     * @return list<int>
     */
    public function getTokenAsArray(): array
    {
        return $this->buffer;
    }

    private function getMatcher(): TokenMatcherInterface
    {
        return $this->matcher ??= new Utf8TokenMatcher();
    }

    private function getTokenFactory(): TokenFactoryInterface
    {
        return $this->tokenFactory ??= new TokenFactory();
    }
}
