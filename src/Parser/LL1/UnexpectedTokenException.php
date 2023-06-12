<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Exception;
use Throwable;

class UnexpectedTokenException extends Exception
{
    public function __construct(
        private UnexpectedTokenErrorInterface $error,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        $message = "Unexpected token: {$this->error->getUnexpectedToken()->getType()}";
        parent::__construct($message, $code, $previous);
    }

    public function getErrorInfo(): UnexpectedTokenErrorInterface
    {
        return $this->error;
    }
}
