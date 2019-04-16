<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Docs;

class User
{
    /** @var string */
    private $name;
    /** @var int */
    private $age;
    /** @var Address */
    private $address;
    /** @var Event[] */
    private $events_attended;

    public function __construct(string $name, int $age, Address $address, array $events_attended = [])
    {
        $this->name = $name;
        $this->age = $age;
        $this->address = $address;
        $this->events_attended = $events_attended;
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

    /**
     * @return Event[]
     */
    public function getEventsAttended(): array
    {
        return $this->events_attended;
    }
}
