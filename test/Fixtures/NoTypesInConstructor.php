<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures;

class NoTypesInConstructor
{
    /** @var mixed */
    private $unknown_property;

    /**
     * @param mixed $unknown_property
     */
    public function __construct($unknown_property)
    {
        $this->unknown_property = $unknown_property;
    }
}
