<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Adapters;

use Burba\StrictJson\Adapter;
use Burba\StrictJson\JsonContext;
use Burba\StrictJson\JsonFormatException;
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Type;

class AdapterThatThrowsJsonFormatException implements Adapter
{

    /**
     * @param $decoded_json
     * @param StrictJson $delegate
     * @param JsonContext $context
     * @throws JsonFormatException
     */
    public function fromJson($decoded_json, StrictJson $delegate, JsonContext $context)
    {
        throw new JsonFormatException("I'm a very bad adapter", $context);
    }

    /**
     * @return Type[]
     */
    public function fromTypes(): array
    {
        return [Type::array()];
    }
}
