<?php namespace Burba\StrictJson\Fixtures;

use Burba\StrictJson\JsonFormatException;
use Burba\StrictJson\StrictJson;

class AdapterThatThrowsJsonFormatException
{

    /**
     * @param StrictJson $delegate
     * @param $parsed_json
     * @throws JsonFormatException
     */
    public function fromJson(/** @noinspection PhpUnusedParameterInspection */ StrictJson $delegate, $parsed_json)
    {
        throw new JsonFormatException("I'm a very bad adapter");
    }
}
