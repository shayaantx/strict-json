<?php

namespace Burba\StrictJson\Fixtures;

class IntPropClass
{
	/** @var int */
	private $int_prop;

	public function __construct(int $int_prop)
	{
		$this->int_prop = $int_prop;
	}
}