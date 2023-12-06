<?php

namespace ChrisReedIO\Socialment\Exceptions;

use Exception;

class AbortedLoginException extends Exception
{
    public function __construct($message = 'Login aborted', $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
