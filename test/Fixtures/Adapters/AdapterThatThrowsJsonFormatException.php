<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Adapters;

use Burba\StrictJson\Adapter;
use Burba\StrictJson\JsonPath;
use Burba\StrictJson\JsonFormatException;
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Type;

class AdapterThatThrowsJsonFormatException implements Adapter
{

    /**
     * @param $decoded_json
     * @param StrictJson $delegate
     * @param JsonPath $path
     * @throws JsonFormatException
     */
    public function fromJson($decoded_json, StrictJson $delegate, JsonPath $path)
    {
        throw new JsonFormatException("I'm a very bad adapter", $path);
    }

    /**
     * @return Type[]
     */
    public function fromTypes(): array
    {
        return [Type::array()];
    }
}
