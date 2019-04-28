<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures;

use RuntimeException;

class ThrowsUnexpectedException
{
    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
        throw new RuntimeException("I've never been the best at constructing");
    }
}
