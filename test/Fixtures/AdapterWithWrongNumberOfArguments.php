<?php namespace Burba\StrictJson\Fixtures;

use Burba\StrictJson\StrictJson;

class AdapterWithWrongNumberOfArguments
{
    public function fromJson(/** @noinspection PhpUnusedParameterInspection */ StrictJson $delegate): ?string
    {
        return null;
    }
}
