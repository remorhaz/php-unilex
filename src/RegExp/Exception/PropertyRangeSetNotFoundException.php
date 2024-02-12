<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Exception;

use OutOfRangeException;
use Throwable;

final class PropertyRangeSetNotFoundException extends OutOfRangeException implements ExceptionInterface
{
    public function __construct(
        private readonly string $propertyName,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Range set not found for Unicode property '$this->propertyName'", previous: $previous);
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }
}
