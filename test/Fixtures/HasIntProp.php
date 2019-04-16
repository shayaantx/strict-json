<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures;

class HasIntProp
{
    /** @var int */
    private $int_prop;

    public function __construct(int $int_prop)
    {
        $this->int_prop = $int_prop;
    }
}
