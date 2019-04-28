<?php declare(strict_types=1);

namespace Burba\StrictJson;

use Exception;
use Throwable;

class JsonFormatException extends Exception
{
    /**
     * @param string $message
     * @param JsonPath $path
     * @param Throwable|null $previous
     */
    public function __construct($message, JsonPath $path, Throwable $previous = null)
    {
        $message = $message . ' at path ' . $path->__toString();
        parent::__construct($message, 0, $previous);
    }
}
