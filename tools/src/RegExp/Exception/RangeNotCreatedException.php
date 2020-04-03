<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Exception;

use RuntimeException;
use Throwable;

final class RangeNotCreatedException extends RuntimeException implements ExceptionInterface
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Failed to create range", 0, $previous);
    }
}
