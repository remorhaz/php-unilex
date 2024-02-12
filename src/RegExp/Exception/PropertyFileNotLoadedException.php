<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Exception;

use RuntimeException;
use Throwable;

final class PropertyFileNotLoadedException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        private readonly string $propertyName,
        private readonly string $propertyFile,
        private readonly ?string $errorMessage,
        ?Throwable $previous = null,
    ) {
        parent::__construct($this->buildMessage(), previous: $previous);
    }

    private function buildMessage(): string
    {
        $message =
            "Failed to load range set for Unicode property '{$this->propertyName}' " .
            "from file {$this->propertyFile}";

        return isset($this->errorMessage)
            ? "$message:\n$this->errorMessage"
            : $message;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getPropertyFile(): string
    {
        return $this->propertyFile;
    }
}
