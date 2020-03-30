<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\AST\Exception;

use DomainException;
use Throwable;

use function gettype;

final class InvalidAttributeException extends DomainException implements ExceptionInterface
{

    private $name;

    private $value;

    private $expectedType;

    public function __construct(string $name, $value, string $expectedType, Throwable $previous = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->expectedType = $expectedType;
        $actualType = gettype($this->value);
        parent::__construct(
            "Node attribute '{$this->name}' has invalid type: {$actualType} instead of expected {$this->expectedType}",
            0,
            $previous
        );
    }
}
