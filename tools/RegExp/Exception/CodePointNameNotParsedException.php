<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Exception;

use LogicException;
use Throwable;

final class CodePointNameNotParsedException extends LogicException implements ExceptionInterface
{

    private $name;

    public function __construct(string $name, Throwable $previous = null)
    {
        parent::__construct("Failed to parse code point name: {$name}", 0, $previous);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
