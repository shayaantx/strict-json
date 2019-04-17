<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Adapters;

use Burba\StrictJson\Adapter;
use Burba\StrictJson\Fixtures\HasIntProp;
use Burba\StrictJson\JsonPath;
use Burba\StrictJson\JsonFormatException;
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Type;

class IntPropClassAdapterThatAddsFour implements Adapter
{
    /**
     * @param array $decoded_json
     * @param StrictJson $delegate
     * @param JsonPath $path
     * @return HasIntProp
     *
     * @throws JsonFormatException
     */
    public function fromJson($decoded_json, StrictJson $delegate, JsonPath $path): HasIntProp
    {
        $original_number = $delegate->mapDecoded(
            $decoded_json['int_prop'],
            Type::int(),
            $path->withProperty('int_prop')
        );
        return new HasIntProp($original_number + 4);
    }

    /**
     * @return Type[]
     */
    public function fromTypes(): array
    {
        return [Type::array()];
    }
}
