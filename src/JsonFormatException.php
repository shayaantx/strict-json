<?php namespace Burba\StrictJson;

use Exception;
use Throwable;

class JsonFormatException extends Exception
{
    public function __construct($message = "", Throwable $previous = null, ?JsonContext $context = null)
    {
        if ($context !== null) {
            $message = $message . ' at path ' . $context->__toString();
        }

        parent::__construct($message, 0, $previous);
    }
}
