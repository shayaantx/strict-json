<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures;

class HasNullableProp
{
    /** @var float|null */
    private $nullable_prop;

    public function __construct(?float $nullable_prop)
    {
        $this->nullable_prop = $nullable_prop;
    }
}
