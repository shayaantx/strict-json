<?php namespace Burba\StrictJson\Fixtures\InvalidAdapters;

use Burba\StrictJson\StrictJson;

class AdapterWithWrongContextArgument
{
    public function fromJson(StrictJson $delegate, $value, string $invalid_type): ?string
    {
        return null;
    }
}
