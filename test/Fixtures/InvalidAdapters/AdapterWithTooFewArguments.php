<?php namespace Burba\StrictJson\Fixtures\InvalidAdapters;

use Burba\StrictJson\StrictJson;

class AdapterWithTooFewArguments
{
    public function fromJson(/** @noinspection PhpUnusedParameterInspection */ StrictJson $delegate): ?string
    {
        return null;
    }
}
