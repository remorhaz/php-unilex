<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Exception;

use RuntimeException;
use Throwable;

final class InvalidLineException extends RuntimeException implements ExceptionInterface
{

    private $lineText;

    public function __construct(string $lineText, Throwable $previous = null)
    {
        $this->lineText = $lineText;
        parent::__construct("Invalid line format: {$this->lineText}", 0, $previous);
    }

    public function getLineText(): string
    {
        return $this->lineText;
    }
}
