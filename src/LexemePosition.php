<?php

namespace Remorhaz\UniLex;

/**
 * Describes lexeme position in symbol buffer.
 */
class LexemePosition
{

    /**
     * Offset of the first symbol of lexeme.
     *
     * @var int
     */
    private $startOffset;

    /**
     * Offset of the next symbol after lexeme.
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
            throw new Exception("Negative start offset in lexeme position: {$startOffset}");
        }
        if ($finishOffset < $startOffset) {
            throw new Exception("Finish offset lesser than start in lexeme position: {$finishOffset} < {$startOffset}");
        }
        $this->startOffset = $startOffset;
        $this->finishOffset = $finishOffset;
    }

    /**
     * Returns offset of the first symbol of lexeme.
     *
     * @return int
     */
    public function getStartOffset(): int
    {
        return $this->startOffset;
    }

    /**
     * Returns the offset of the next symbol after lexeme.
     *
     * @return int
     */
    public function getFinishOffset(): int
    {
        return $this->finishOffset;
    }

    /**
     * Returns length of the lexeme in symbols.
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->finishOffset - $this->startOffset;
    }
}
