<?php namespace Burba\StrictJson\Fixtures\Example;

use Burba\StrictJson\StrictJson;

class LenientBooleanAdapter
{
	public function fromJson(StrictJson $delegate, $parsed_value): bool
	{
		return (bool)$parsed_value;
	}
}