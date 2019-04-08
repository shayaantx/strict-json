<?php namespace Burba\StrictJson;

use RuntimeException;
use Throwable;

class InvalidConfigurationException extends RuntimeException
{
	public function __construct($message = "", Throwable $previous = null)
	{
		parent::__construct($message, 0, $previous);
	}
}