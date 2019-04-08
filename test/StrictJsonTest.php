<?php namespace Burba\StrictJson;

use Burba\StrictJson\Fixtures\BasicClass;
use Burba\StrictJson\Fixtures\ClassPropClass;
use Burba\StrictJson\Fixtures\IntArrayPropClass;
use Burba\StrictJson\Fixtures\IntPropClass;
use Burba\StrictJson\Fixtures\IntPropClassAdapterThatAddsFour;
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
}
