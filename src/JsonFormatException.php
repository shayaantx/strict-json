<?php namespace Burba\StrictJson;

use Exception;
use Throwable;

class JsonFormatException extends Exception
{
    public function __construct($message = "", Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
