<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures;

class NoTypesInConstructor
{
    private $unknown_property;

    public function __construct($unknown_property)
    {
        $this->unknown_property = $unknown_property;
    }
}
