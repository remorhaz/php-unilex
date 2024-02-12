<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\Exception;

/**
 * Describes token position in symbol buffer.
 */
class TokenPosition
{
    /**
     * @param int $startOffset  Offset of the first symbol of token.
     * @param int $finishOffset Offset of the next symbol after token.
     * @throws Exception
     */
    public function __construct(
        private readonly int $startOffset,
        private readonly int $finishOffset,
    ) {
        if ($this->startOffset < 0) {
            throw new Exception("Negative start offset in token position: $this->startOffset");
        }
        if ($this->finishOffset < $this->startOffset) {
            throw new Exception(
                "Finish offset lesser than start in token position: $this->finishOffset < $this->startOffset",
            );
        }
    }

    /**
     * Returns offset of the first symbol of token.
     */
    public function getStartOffset(): int
    {
        return $this->startOffset;
    }

    /**
     * Returns the offset of the next symbol after token.
     */
    public function getFinishOffset(): int
    {
        return $this->finishOffset;
    }

    /**
     * Returns length of the token in symbols.
     */
    public function getLength(): int
    {
        return $this->finishOffset - $this->startOffset;
    }
}
