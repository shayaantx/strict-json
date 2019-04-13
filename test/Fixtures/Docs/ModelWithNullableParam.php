<?php namespace Burba\StrictJson\Fixtures\Docs;

class ModelWithNullableParam
{
    private $nullable_param;

    public function __construct(?string $nullable_param)
    {
        $this->nullable_param = $nullable_param;
    }

    public function getNullableParam(): ?string
    {
        return $this->nullable_param;
    }
}
