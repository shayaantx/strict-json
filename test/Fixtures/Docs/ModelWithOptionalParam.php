<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Docs;

class ModelWithOptionalParam
{
    private $optional_param;

    public function __construct(string $optional_param = 'default')
    {
        $this->optional_param = $optional_param;
    }

    public function getOptionalParam(): string
    {
        return $this->optional_param;
    }
}
