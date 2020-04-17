<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Exception;

use Remorhaz\IntRangeSets\RangeSetInterface;
use Throwable;
use UnexpectedValueException;

use function get_class;
use function gettype;
use function is_object;

final class InvalidPropertyRangeSetException extends UnexpectedValueException implements ExceptionInterface
{

    private $propertyName;

    private $propertyFile;

    private $rangeSet;

    public function __construct(string $propertyName, string $propertyFile, $rangeSet, Throwable $previous = null)
    {
        $this->propertyName = $propertyName;
        $this->propertyFile = $propertyFile;
        $this->rangeSet = $rangeSet;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        $actualType = is_object($this->rangeSet)
            ? get_class($this->rangeSet)
            : gettype($this->rangeSet);
        $expectedType = RangeSetInterface::class;

        return
            "Invalid range set loaded from {$this->propertyFile} for Unicode property '{$this->propertyName}':\n" .
            "{$actualType} instead of {$expectedType}";
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getPropertyFile(): string
    {
        return $this->propertyFile;
    }

    public function getRangeSet()
    {
        return $this->rangeSet;
    }
}
