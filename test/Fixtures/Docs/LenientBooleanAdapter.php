<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Docs;

use Burba\StrictJson\Adapter;
use Burba\StrictJson\JsonPath;
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Type;

class LenientBooleanAdapter implements Adapter
{
    public function fromJson($decoded_json, StrictJson $delegate, JsonPath $path): bool
    {
        return (bool)$decoded_json;
    }

    /**
     * @return Type[]
     */
    public function fromTypes(): array
    {
        return [
            Type::int(),
            Type::bool(),
        ];
    }
}
