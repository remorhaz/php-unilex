<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

class TokenBuffer implements CharBufferInterface, TokenExtractInterface
{

    private $reader;

    private $symbolFactory;

    /**
     * @var Token[]
     */
    private $data = [];

    private $startOffset = 0;

    private $previewOffset = 0;

    public function __construct(TokenReaderInterface $reader, SymbolFactoryInterface $symbolFactory)
    {
        $this->reader = $reader;
        $this->symbolFactory = $symbolFactory;
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
        $token->setBufferInfo($this->getTokenInfo());
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
        return $this->symbolFactory->getSymbol($this->getToken());
    }

    /**
     * @return TokenBufferInfoInterface
     * @throws Exception
     * @todo Attach merged source input info, maybe?
     */
    private function getTokenInfo(): TokenBufferInfoInterface
    {
        $position = new TokenPosition($this->startOffset, $this->previewOffset);
        return new TokenBufferInfo($this, $position);
    }

    public function extractToken(TokenPosition $position): SplFixedArray
    {
        $startOffset = $position->getStartOffset();
        $tokenLength = $position->getLength();
        $output = new SplFixedArray($tokenLength);
        for ($i = 0; $i < $tokenLength; $i++) {
            $token = $this->data[$startOffset + $i];
            $symbol = $this->symbolFactory->getSymbol($token);
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
