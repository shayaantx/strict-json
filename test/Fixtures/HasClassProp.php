<?php namespace Burba\StrictJson\Fixtures;

class HasClassProp
{
    /** @var HasIntProp */
    private $int_prop_class;

    public function __construct(HasIntProp $int_prop_class)
    {
        $this->int_prop_class = $int_prop_class;
    }
}
