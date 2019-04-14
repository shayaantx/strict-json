<?php namespace Burba\StrictJson\Fixtures;

use Burba\StrictJson\JsonFormatException;
use Burba\StrictJson\StrictJson;

class IntPropClassAdapterThatAddsFour
{
    /**
     * @param StrictJson $delegate
     * @param array $parsed_json
     * @return HasIntProp
     *
     * @throws JsonFormatException
     */
    public function fromJson(StrictJson $delegate, array $parsed_json): HasIntProp
    {
        $original_number = $delegate->mapDecoded($parsed_json['int_prop'], 'int');
        return new HasIntProp($original_number + 4);
    }
}
