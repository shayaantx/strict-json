<?php declare(strict_types=1);

use Burba\StrictJson\Fixtures\Docs\Address;
use Burba\StrictJson\Fixtures\Docs\Event;
use Burba\StrictJson\Fixtures\Docs\User;
use Burba\StrictJson\JsonFormatException;
use Burba\StrictJson\StrictJson;

/**
 * @BeforeMethods({"setup"})
 * @Iterations(100)
 */
class LargeListBench
{
    /** @var StrictJson */
    private $mapper;
    /** @var string */
    private $json;

    public function setup()
    {
        $this->json = file_get_contents(__DIR__ . '/fixture/large_list.json');

        $this->mapper = StrictJson::builder()
            ->addParameterArrayAdapter(User::class, 'events_attended', Event::class)
            ->build();
    }

    /**
     * @throws JsonFormatException
     */
    public function benchLargeList()
    {
        $this->mapper->mapToArrayOf($this->json, User::class);
    }

    public function benchLargeListHandwritten()
    {
        $decoded_json = json_decode($this->json, true);
        if (!is_array($decoded_json)) {
            throw new RuntimeException('Invalid JSON');
        }

        $users = [];
        foreach ($decoded_json as $json_object) {
            $name = $json_object['name'] ?? null;
            $age = $json_object['age'] ?? null;
            $street = $json_object['address']['street'] ?? null;
            $zip_code = $json_object['address']['zip_code'] ?? null;

            if (!is_string($name) || !is_int($age) || !is_string($street) || !is_string($zip_code)) {
                throw new RuntimeException('Invalid JSON');
            }

            $address = new Address($street, $zip_code);
            $users[] = new User($name, $age, $address);
        }
    }
}
