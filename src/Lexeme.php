<?php

namespace Remorhaz\UniLex;

class Lexeme
{

    private $type;

    private $isEoi;

    private $bufferInfo;

    private $matcherInfo;

    public function __construct(int $type, bool $isEoi)
    {
        $this->type = $type;
        $this->isEoi = $isEoi;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function isEoi(): bool
    {
        return $this->isEoi;
    }

    public function setBufferInfo(LexemeBufferInfoInterface $bufferInfo): void
    {
        $this->bufferInfo = $bufferInfo;
    }

    public function getBufferInfo(): ?LexemeBufferInfoInterface
    {
        return $this->bufferInfo;
    }

    public function setMatcherInfo(LexemeMatcherInfoInterface $matcherInfo): void
    {
        $this->matcherInfo = $matcherInfo;
    }

    public function getMatcherInfo(): ?LexemeMatcherInfoInterface
    {
        return $this->matcherInfo;
    }
}
