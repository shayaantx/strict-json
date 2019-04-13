<?php namespace Burba\StrictJson\Fixtures\Docs;

use Burba\StrictJson\StrictJson;
use DateTime;

class DateAdapter
{
    public function fromJson(StrictJson $delegate, string $parsed_json): DateTime
    {
        return DateTime::createFromFormat(DateTime::ISO8601, $parsed_json);
    }
}
