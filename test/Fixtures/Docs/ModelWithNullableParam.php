<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Docs;

class ModelWithNullableParam
{
    /** @var string|null */
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
