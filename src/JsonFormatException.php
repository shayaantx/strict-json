<?php declare(strict_types=1);

namespace Burba\StrictJson;

use Exception;
use Throwable;

class JsonFormatException extends Exception
{
    public function __construct($message, JsonContext $context, Throwable $previous = null)
    {
        $message = $message . ' at path ' . $context->__toString();
        parent::__construct($message, 0, $previous);
    }
}
