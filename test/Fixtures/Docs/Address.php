<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Docs;

class Address
{
    /** @var string */
    private $street;
    /** @var string */
    private $zip_code;

    public function __construct(string $street, string $zip_code)
    {
        $this->street = $street;
        $this->zip_code = $zip_code;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zip_code;
    }
}
