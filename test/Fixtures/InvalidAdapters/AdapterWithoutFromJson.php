<?php namespace Burba\StrictJson\Fixtures\InvalidAdapters;

class AdapterWithoutFromJson
{
    public function toJson()
    {
        return 'This should have been fromJson';
    }
}
