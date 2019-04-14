<?php namespace Burba\StrictJson\Fixtures\InvalidAdapters;

use Burba\StrictJson\JsonContext;
use Burba\StrictJson\StrictJson;

class AdapterWithTooManyArguments
{
    public function fromJson(StrictJson $delegate, $value, JsonContext $context, $another_unexpected_value): ?string
    {
        return null;
    }
}
