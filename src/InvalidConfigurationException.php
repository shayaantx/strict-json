<?php declare(strict_types=1);

namespace Burba\StrictJson;

use RuntimeException;
use Throwable;

class InvalidConfigurationException extends RuntimeException
{
    public function __construct(string $message, JsonPath $path, Throwable $previous = null)
    {
        $message = $message . ' at path ' . $path->__toString();
        parent::__construct($message, 0, $previous);
    }
}
