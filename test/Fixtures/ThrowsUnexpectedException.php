<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures;

use RuntimeException;

class ThrowsUnexpectedException
{
    public function __construct(/** @noinspection PhpUnusedParameterInspection */ string $value)
    {
        throw new RuntimeException("I've never been the best at constructing");
    }
}
