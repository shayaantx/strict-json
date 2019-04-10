<?php

namespace Burba\StrictJson\Fixtures;

class IntArrayPropClass
{
    /** @var int[] */
    private $int_array_prop;

    public function __construct(array $int_array_prop)
    {
        $this->int_array_prop = $int_array_prop;
    }
}
