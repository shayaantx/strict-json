<?php namespace Burba\StrictJson\Fixtures\Example;

use DateTime;

class Event
{
	/** @var string */
	private $name;
	/** @var DateTime */
	private $date;

	public function __construct(string $name, DateTime $date)
	{
		$this->name = $name;
		$this->date = $date;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return DateTime
	 */
	public function getDate(): DateTime
	{
		return $this->date;
	}
}