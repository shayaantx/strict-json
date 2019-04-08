<?php namespace Burba\StrictJson\Fixtures;

use Burba\StrictJson\JsonFormatException;
use Burba\StrictJson\StrictJson;

class IntPropClassAdapterThatAddsFour
{
	/**
	 * @param StrictJson $delegate
	 * @param array $parsed_json
	 * @return IntPropClass
	 *
	 * @throws JsonFormatException
	 */
	public function fromJson(StrictJson $delegate, array $parsed_json): IntPropClass
	{
		$original_number = $delegate->mapParsed($parsed_json['int_prop'], 'int');
		return new IntPropClass($original_number + 4);
	}
}