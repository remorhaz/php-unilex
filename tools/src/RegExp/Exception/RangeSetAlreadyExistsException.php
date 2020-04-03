<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Exception;

use LogicException;
use Throwable;

final class RangeSetAlreadyExistsException extends LogicException implements ExceptionInterface
{

    private $propertyName;

    public function __construct(string $propertyName, Throwable $previous = null)
    {
        $this->propertyName = $propertyName;
        parent::__construct("Property '{$this->propertyName}' already exists", 0, $previous);
    }
}