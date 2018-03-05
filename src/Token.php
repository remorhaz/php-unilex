<?php

namespace Remorhaz\UniLex;

class Token
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

    public function setBufferInfo(TokenBufferInfoInterface $bufferInfo): void
    {
        $this->bufferInfo = $bufferInfo;
    }

    public function getBufferInfo(): ?TokenBufferInfoInterface
    {
        return $this->bufferInfo;
    }

    public function setMatcherInfo(TokenMatcherInfoInterface $matcherInfo): void
    {
        $this->matcherInfo = $matcherInfo;
    }

    public function getMatcherInfo(): ?TokenMatcherInfoInterface
    {
        return $this->matcherInfo;
    }
}
