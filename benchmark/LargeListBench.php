<?php declare(strict_types=1);

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
}
