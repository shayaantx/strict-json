<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Adapters;

use Burba\StrictJson\Adapter;
use Burba\StrictJson\JsonPath;
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Type;

class AdapterThatSupportsNoTypes implements Adapter
{
    public function fromJson($decoded_json, StrictJson $delegate, JsonPath $path): ?string
    {
        return null;
    }

    /** @return Type[] */
    public function fromTypes(): array
    {
        return [];
    }
}
