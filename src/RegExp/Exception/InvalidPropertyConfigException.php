<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Exception;

use DomainException;
use Throwable;

use function gettype;

final class InvalidPropertyConfigException extends DomainException implements ExceptionInterface
{
    public function __construct(
        private readonly string $propertyName,
        private readonly mixed $propertyFile,
        ?Throwable $previous = null,
    ) {
        parent::__construct($this->buildMessage(), previous: $previous);
    }

    private function buildMessage(): string
    {
        $fileNameType = gettype($this->propertyFile);

        return
            "Invalid config for Unicode property '$this->propertyName': " .
            "$fileNameType instead of string filename";
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getPropertyFile(): mixed
    {
        return $this->propertyFile;
    }
}
