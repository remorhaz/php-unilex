<?php

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\IO\CharFactoryInterface;
use Remorhaz\UniLex\IO\TokenExtractInterface;
use SplFixedArray;

class TokenBuffer implements CharBufferInterface, TokenExtractInterface
{

    private const DEFAULT_TOKEN_ATTRIBUTE_PREFIX = 'token';

    private $reader;

    private $symbolFactory;

    /**
     * @var Token[]
     */
    private $data = [];

    private $startOffset = 0;

    private $previewOffset = 0;

    private $tokenAttributePrefix;

    public function __construct(
        TokenReaderInterface $reader,
        CharFactoryInterface $symbolFactory,
        string $tokenAttributePrefix = self::DEFAULT_TOKEN_ATTRIBUTE_PREFIX
    ) {
        $this->reader = $reader;
        $this->symbolFactory = $symbolFactory;
        $this->tokenAttributePrefix = $tokenAttributePrefix;
    }

    public function isEnd(): bool
    {
        return $this->getToken()->isEoi();
    }

    /**
     * @throws Exception
     */
    public function nextSymbol(): void
    {
        if ($this->isEnd()) {
            throw new Exception("Unexpected end of buffer at index {$this->previewOffset}");
        }
        $this->cacheToken();
        $this->previewOffset++;
    }

    public function resetToken(): void
    {
        $this->previewOffset = $this->startOffset;
    }

    /**
     * @param Token $token
     * @throws Exception
     */
    public function finishToken(Token $token): void
    {
        $token->setAttribute("{$this->tokenAttributePrefix}.position.start", $this->startOffset);
        $token->setAttribute("{$this->tokenAttributePrefix}.position.finish", $this->previewOffset);
        $this->startOffset = $this->previewOffset;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getSymbol(): int
    {
        if ($this->isEnd()) {
            throw new Exception("No symbol to preview at index {$this->previewOffset}");
        }
        return $this->symbolFactory->getChar($this->getToken());
    }

    public function extractToken(TokenPosition $position): SplFixedArray
    {
        $startOffset = $position->getStartOffset();
        $tokenLength = $position->getLength();
        $output = new SplFixedArray($tokenLength);
        for ($i = 0; $i < $tokenLength; $i++) {
            $token = $this->data[$startOffset + $i];
            $symbol = $this->symbolFactory->getChar($token);
            $output->offsetSet($i, $symbol);
        }
        return $output;
    }

    private function getToken(): Token
    {
        $this->cacheToken();
        return $this->data[$this->previewOffset];
    }

    private function cacheToken(): void
    {
        if (!isset($this->data[$this->previewOffset])) {
            $this->data[$this->previewOffset] = $this->reader->read();
        }
    }
}
