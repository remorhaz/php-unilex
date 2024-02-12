<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\AST\Exception;

use DomainException;
use Throwable;

use function gettype;

final class InvalidAttributeException extends DomainException implements ExceptionInterface
{
    public function __construct(
        private readonly string $name,
        private readonly mixed $value,
        private readonly string $expectedType,
        ?Throwable $previous = null,
    ) {
        $actualType = gettype($this->value);
        parent::__construct(
            message: "Node attribute '$this->name' has invalid type: " .
                "$actualType instead of expected $this->expectedType",
            previous: $previous,
        );
    }
}
