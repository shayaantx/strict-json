<?php
declare(strict_types=1);

namespace Burba\StrictJson\Fixtures;

class HasObjectProp
{
    /** @var object */
    private $object;

    public function __construct(object $object)
    {
        $this->object = $object;
    }
}
