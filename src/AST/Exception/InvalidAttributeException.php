<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\AST\Exception;

use DomainException;
use Throwable;

use function gettype;

final class InvalidAttributeException extends DomainException implements ExceptionInterface
{
    public function __construct(
        private string $name,
        private mixed $value,
        private string $expectedType,
        ?Throwable $previous = null,
    ) {
        $actualType = gettype($this->value);
        parent::__construct(
            "Node attribute '$this->name' has invalid type: $actualType instead of expected $this->expectedType",
            0,
            $previous,
        );
    }
}
