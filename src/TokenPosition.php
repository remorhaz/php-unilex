<?php

namespace Remorhaz\UniLex;

/**
 * Describes token position in symbol buffer.
 */
class TokenPosition
{

    /**
     * Offset of the first symbol of token.
     *
     * @var int
     */
    private $startOffset;

    /**
     * Offset of the next symbol after token.
     *
     * @var int
     */
    private $finishOffset;

    /**
     * Constructor.
     * .
     * @param int $startOffset
     * @param int $finishOffset
     * @throws Exception
     */
    public function __construct(int $startOffset, int $finishOffset)
    {
        if ($startOffset < 0) {
            throw new Exception("Negative start offset in token position: {$startOffset}");
        }
        if ($finishOffset < $startOffset) {
            throw new Exception("Finish offset lesser than start in token position: {$finishOffset} < {$startOffset}");
        }
        $this->startOffset = $startOffset;
        $this->finishOffset = $finishOffset;
    }

    /**
     * Returns offset of the first symbol of token.
     *
     * @return int
     */
    public function getStartOffset(): int
    {
        return $this->startOffset;
    }

    /**
     * Returns the offset of the next symbol after token.
     *
     * @return int
     */
    public function getFinishOffset(): int
    {
        return $this->finishOffset;
    }

    /**
     * Returns length of the token in symbols.
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->finishOffset - $this->startOffset;
    }
}
