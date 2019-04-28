<?php
declare(strict_types=1);

namespace Burba\StrictJson\Fixtures;

use InvalidArgumentException;

class ThrowsInvalidArgumentException
{
    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
        throw new InvalidArgumentException('I am very picky');
    }
}
