<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidRequestOperation extends Exception
{
    public function __construct(
        $message = "There is something wrong with the request system",
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
