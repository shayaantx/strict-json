<?php namespace Burba\StrictJson\Fixtures\Example;

use DateTime;

class Event
{
	/** @var string */
	private $name;
	/** @var DateTime */
	private $date;
	/** @var bool */
	private $is_suit_required;

	public function __construct(string $name, DateTime $date, bool $is_suit_required = false)
	{
		$this->name = $name;
		$this->date = $date;
		$this->is_suit_required = $is_suit_required;
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

	public function isSuitRequired(): bool
	{
		return $this->is_suit_required;
	}
}