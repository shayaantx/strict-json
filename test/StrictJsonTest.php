<?php namespace Burba\StrictJson;

use Burba\StrictJson\Fixtures\BasicClass;
use Burba\StrictJson\Fixtures\ClassPropClass;
use Burba\StrictJson\Fixtures\Example\Address;
use Burba\StrictJson\Fixtures\Example\DateAdapter;
use Burba\StrictJson\Fixtures\Example\Event;
use Burba\StrictJson\Fixtures\Example\LenientBooleanAdapter;
use Burba\StrictJson\Fixtures\Example\User;
use Burba\StrictJson\Fixtures\IntArrayPropClass;
use Burba\StrictJson\Fixtures\IntPropClass;
use Burba\StrictJson\Fixtures\IntPropClassAdapterThatAddsFour;
use DateTime;
use PHPUnit\Framework\TestCase;

class StrictJsonTest extends TestCase
{
	/**
	 * @throws JsonFormatException
	 */
	public function testBasicCase()
	{
		$json = '
		{
			"string_prop": "string_value",
			"int_prop": 1,
			"float_prop": 1.2,
			"bool_prop": true,
			"array_prop": [1, 2, 3],
			"class_prop": {
				"int_prop": 5
			}
		}
		';

		$mapper = new StrictJson();
		$this->assertEquals(
			new BasicClass(
				'string_value', 1, 1.2, true, [1, 2, 3], new IntPropClass(5)
			),
			$mapper->map($json, BasicClass::class)
		);
	}

	/**
	 * @throws JsonFormatException
	 */
	public function testIntArrayProperty()
	{
		$json = '{ "int_array_prop": [1, 2, 3] }';
		$mapper = StrictJson::builder()
			->addParameterAdapter(
				IntArrayPropClass::class,
				'int_array_prop',
				new ArrayAdapter('int')
			)
			->build();

		$this->assertEquals(
			new IntArrayPropClass([1, 2, 3]),
			$mapper->map($json, IntArrayPropClass::class)
		);
	}

	/**
	 * @throws JsonFormatException
	 */
	public function testClassAdapterForRootObject()
	{
		$mapper = StrictJson::builder()
			->addClassAdapter(IntPropClass::class, new IntPropClassAdapterThatAddsFour())
			->build();

		$json = '{ "int_prop": 1 }';
		$this->assertEquals(
			new IntPropClass(5),
			$mapper->map($json, IntPropClass::class)
		);
	}

	/**
	 * @throws JsonFormatException
	 */
	public function testClassAdapterForProperty()
	{
		$mapper = StrictJson::builder()
			->addClassAdapter(IntPropClass::class, new IntPropClassAdapterThatAddsFour())
			->build();

		$json = '{ "int_prop_class": { "int_prop": 1 } }';
		$this->assertEquals(
			new ClassPropClass(new IntPropClass(5)),
			$mapper->map($json, ClassPropClass::class)
		);
	}

	/**
	 * @throws JsonFormatException
	 */
	public function testReadmeBasicExample()
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
	public function testReadmeAdapterExample()
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
