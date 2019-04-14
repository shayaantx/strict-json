<?php namespace Burba\StrictJson\Fixtures\InvalidAdapters;

class AdapterWithWrongDelegateArgument
{
    public function fromJson(string $wrong_type, $value): ?string
    {
        return null;
    }
}
