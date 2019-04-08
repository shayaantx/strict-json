<?php namespace Burba\StrictJson\Fixtures\Example;

class User
{
	/** @var string */
	private $name;
	/** @var int */
	private $age;
	/** @var Address */
	private $address;

	public function __construct(string $name, int $age, Address $address)
	{
		$this->name = $name;
		$this->age = $age;
		$this->address = $address;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getAge(): int
	{
		return $this->age;
	}

	/**
	 * @return Address
	 */
	public function getAddress(): Address
	{
		return $this->address;
	}
}

