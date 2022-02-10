<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Exception;
use Throwable;

class UnexpectedTokenException extends Exception
{
    private $error;

    public function __construct(UnexpectedTokenErrorInterface $error, int $code = 0, Throwable $previous = null)
    {
        $message = "Unexpected token: {$error->getUnexpectedToken()->getType()}";
        parent::__construct($message, $code, $previous);
        $this->error = $error;
    }

    public function getErrorInfo(): UnexpectedTokenErrorInterface
    {
        return $this->error;
    }
}
