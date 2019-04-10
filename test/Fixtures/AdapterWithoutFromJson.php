<?php namespace Burba\StrictJson\Fixtures;

class AdapterWithoutFromJson
{
    public function toJson()
    {
        return 'This should have been fromJson';
    }
}
