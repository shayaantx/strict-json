<?php namespace Burba\StrictJson;

use Burba\StrictJson\Fixtures\Example\Address;
use Burba\StrictJson\Fixtures\Example\DateAdapter;
use Burba\StrictJson\Fixtures\Example\Event;
use Burba\StrictJson\Fixtures\Example\LenientBooleanAdapter;
use Burba\StrictJson\Fixtures\Example\User;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Suite of tests that verify that the Readme examples work
 */
class ReadmeTest extends TestCase
{
	/**
	 * @throws JsonFormatException
	 */
	public function testBasicExample()
	{
		$json = '
		{
		  "name": "Joe User",
		  "age": 4,
		  "address": {
			"street": "1234 Fake St.",
			"zip_code": "12345"
		  }
		}
		';
		$mapper = new StrictJson();

		$this->assertEquals(
			new User('Joe User', 4, new Address('1234 Fake St.', '12345')),
			$mapper->map($json, User::class)
		);
	}

	/**
	 * @throws JsonFormatException
	 */
	public function testAdapterExample()
	{
		$json = '
		{
			"name": "Dinner party for Bob",
			"date": "2013-02-13T08:35:34Z"
		}
		';

		$mapper = StrictJson::builder()->addClassAdapter(DateTime::class, new DateAdapter())->build();
		/** @var Event $event */
		$event = $mapper->map($json, Event::class);
		$this->assertEquals('2013', $event->getDate()->format('Y'));
	}

	/**
	 * @throws JsonFormatException
	 */
	public function testReadmeParameterAdapterExample()
	{
		$json = '
		{
			"name": "Dinner party for Bob",
			"date": "2013-02-13T08:35:34Z",
			"is_suit_required": 1
		}
		';

		$mapper = StrictJson::builder()
			->addClassAdapter(DateTime::class, new DateAdapter())
			->addParameterAdapter(Event::class, 'is_suit_required', new LenientBooleanAdapter())
			->build();

		/** @var Event $event */
		$event = $mapper->map($json, Event::class);
		$this->assertTrue($event->isSuitRequired());
	}

	/**
	 * @throws JsonFormatException
	 */
	public function testReadmeArrayAdapterExample()
	{
		$json = '
		{
			"name": "Tim Fabulous",
			"age": 40,
			"address": {
				"street": "1234 Fake St.",
				"zip_code": "12345"
			},
			"events_attended": [
				{
					"name": "Dinner party for Bob",
					"date": "2013-02-13T08:35:34Z"
				}
			]
		}
		';

		$mapper = StrictJson::builder()
			->addClassAdapter(DateTime::class, new DateAdapter())
			->addParameterArrayAdapter(User::class, 'events_attended', Event::class)
			->build();

		/** @var User $user */
		$user = $mapper->map($json, User::class);
		$this->assertEquals(
			'Dinner party for Bob',
			$user->getEventsAttended()[0]->getName()
		);
	}
}