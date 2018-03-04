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

    public function setBufferInfo(LexemeInfoInterface $bufferInfo): void
    {
        $this->bufferInfo = $bufferInfo;
    }

    public function getBufferInfo(): ?LexemeInfoInterface
    {
        return $this->bufferInfo;
    }

    public function setMatcherInfo(MatcherInfoInterface $matcherInfo): void
    {
        $this->matcherInfo = $matcherInfo;
    }

    public function getMatcherInfo(): ?MatcherInfoInterface
    {
        return $this->matcherInfo;
    }
}
