<?php namespace Burba\StrictJson\Fixtures;

class HasNullableProp
{
    /** @var string|null */
    private $nullable_prop;

    public function __construct(?string $nullable_prop)
    {
        $this->nullable_prop = $nullable_prop;
    }
}
