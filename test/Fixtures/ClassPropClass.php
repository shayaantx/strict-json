<?php namespace Burba\StrictJson\Fixtures;

class ClassPropClass
{
    /** @var IntPropClass */
    private $int_prop_class;

    public function __construct(IntPropClass $int_prop_class)
    {
        $this->int_prop_class = $int_prop_class;
    }
}
