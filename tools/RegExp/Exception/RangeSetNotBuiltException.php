<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Exception;

use LogicException;
use Throwable;

final class RangeSetNotBuiltException extends LogicException implements ExceptionInterface
{

    private $propertyName;

    public function __construct(string $propertyName, Throwable $previous = null)
    {
        $this->propertyName = $propertyName;
        parent::__construct(
            "Failed to build range set for property '{$this->propertyName}'",
            0,
            $previous
        );
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }
}
