<?php namespace Burba\StrictJson\Fixtures\Docs;

use Burba\StrictJson\StrictJson;

class LenientBooleanAdapter
{
    public function fromJson(StrictJson $delegate, $parsed_value): bool
    {
        return (bool)$parsed_value;
    }
}
