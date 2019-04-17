<?php
declare(strict_types=1);

namespace Burba\StrictJson\Fixtures;

use InvalidArgumentException;

class ThrowsInvalidArgumentException
{
    public function __construct(/** @noinspection PhpUnusedParameterInspection */ string $value)
    {
        throw new InvalidArgumentException('I am very picky');
    }
}
