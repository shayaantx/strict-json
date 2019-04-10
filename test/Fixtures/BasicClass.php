<?php

namespace Burba\StrictJson\Fixtures;

class BasicClass
{
    /** @var string */
    private $string_prop;
    /** @var int */
    private $int_prop;
    /** @var float */
    private $float_prop;
    /** @var bool */
    private $bool_prop;
    /** @var array */
    private $array_prop;
    /** @var int|null */
    private $nullable_int_prop;
    /** @var IntPropClass */
    private $class_prop;

    public function __construct(
        string $string_prop,
        int $int_prop,
        float $float_prop,
        bool $bool_prop,
        array $array_prop,
        IntPropClass $class_prop,
        ?int $nullable_int_prop = null
    ) {
        $this->string_prop = $string_prop;
        $this->int_prop = $int_prop;
        $this->float_prop = $float_prop;
        $this->bool_prop = $bool_prop;
        $this->array_prop = $array_prop;
        $this->class_prop = $class_prop;
        $this->nullable_int_prop = $nullable_int_prop;
    }
}
