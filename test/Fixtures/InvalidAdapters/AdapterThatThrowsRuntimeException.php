<?php namespace Burba\StrictJson\Fixtures\InvalidAdapters;

use Burba\StrictJson\StrictJson;
use RuntimeException;

class AdapterThatThrowsRuntimeException
{
    public function fromJson(/** @noinspection PhpUnusedParameterInspection */ StrictJson $delegate, $parsed_json)
    {
        throw new RuntimeException("I'm a very bad adapter");
    }
}
