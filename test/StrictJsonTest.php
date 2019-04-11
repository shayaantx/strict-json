<?php namespace Burba\StrictJson;

use Burba\StrictJson\Fixtures\AdapterThatThrowsJsonFormatException;
use Burba\StrictJson\Fixtures\AdapterThatThrowsRuntimeException;
use Burba\StrictJson\Fixtures\AdapterWithoutFromJson;
use Burba\StrictJson\Fixtures\AdapterWithWrongNumberOfArguments;
use Burba\StrictJson\Fixtures\BasicClass;
use Burba\StrictJson\Fixtures\ClassPropClass;
use Burba\StrictJson\Fixtures\Example\User;
use Burba\StrictJson\Fixtures\IntArrayPropClass;
use Burba\StrictJson\Fixtures\IntPropClass;
use Burba\StrictJson\Fixtures\IntPropClassAdapterThatAddsFour;
use Burba\StrictJson\Fixtures\MissingConstructorClass;
use Burba\StrictJson\Fixtures\NoTypesInConstructor;
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
                'string_value',
                1,
                1.2,
                true,
                [1, 2, 3],
                new IntPropClass(5)
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
    public function testInvalidJson()
    {
        $mapper = new StrictJson();
        $json = '{ invalid';
        $this->expectException(JsonFormatException::class);
        $this->expectExceptionMessage("Unable to parse invalid JSON (Syntax error): $json");
        $mapper->map($json, User::class);
    }

    /**
     * @throws JsonFormatException
     */
    public function testInvalidTargetType()
    {
        $mapper = new StrictJson();
        $json = '{"does_not": "matter"}';
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Target type "invalid" is not a scalar type or valid class and has no registered type adapter');
        $mapper->map($json, 'invalid');
    }

    /**
     * Verify that passing invalid values in for class adapters always throws a config exception
     *
     * @param $adapter
     * @param $expected_exception_message
     * @throws JsonFormatException
     * @dataProvider invalidAdapterProvider
     */
    public function testInvalidClassAdapter($adapter, $expected_exception_message)
    {
        $mapper = new StrictJson([IntPropClass::class => $adapter]);
        $json = '{"does_not": "matter"}';
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($expected_exception_message);
        $mapper->map($json, IntPropClass::class);
    }

    /**
     * Verify that passing invalid values in for parameter adapters always throws a config exception
     *
     * @throws JsonFormatException
     * @dataProvider invalidAdapterProvider
     */
    public function testInvalidParameterAdapter($adapter)
    {
        $mapper = new StrictJson([], [IntPropClass::class => ['int_prop' => $adapter]]);
        $json = '{ "int_prop": 1 }';
        $this->expectException(InvalidConfigurationException::class);
        $mapper->map($json, IntPropClass::class);
    }

    /**
     * @throws JsonFormatException
     */
    public function testClassAdapterThatThrowsJsonFormatException()
    {
        $mapper = new StrictJson([IntPropClass::class => new AdapterThatThrowsJsonFormatException()]);
        $json = '{"does_not": "matter"}';
        $this->expectException(JsonFormatException::class);
        $mapper->map($json, IntPropClass::class);
    }

    /**
     * Verify that parsing json with types that don't match the target class' constructor args throws a
     * JsonFormatException
     * @throws JsonFormatException
     */
    public function testMismatchedTypes()
    {
        $mapper = new StrictJson();
        $json = '{"int_prop": "1"}';
        $this->expectException(JsonFormatException::class);
        $mapper->map($json, IntPropClass::class);
    }

    /**
     * Verify that trying to map to a class that has constructor arguments that don't have types throws an
     * InvalidFormatException
     * @throws JsonFormatException
     */
    public function testClassWithNonTypedConstructorArgs()
    {
        $mapper = new StrictJson();
        $json = '{"unknown_property": "value"}';
        $this->expectException(InvalidConfigurationException::class);
        $mapper->map($json, NoTypesInConstructor::class);
    }

    /**
     * @throws JsonFormatException
     */
    public function testMissingProperty()
    {
        $mapper = new StrictJson();
        $json = '{"unknown_property": "value"}';
        $this->expectException(JsonFormatException::class);
        $mapper->map($json, IntPropClass::class);
    }

    /**
     * @throws JsonFormatException
     */
    public function testJsonHasWrongItemType()
    {
        $mapper = StrictJson::builder()
            ->addParameterArrayAdapter(IntArrayPropClass::class, 'int_array_prop', 'int')
            ->build();
        $json = '{"int_array_prop": [1, "2", 3]}';
        $this->expectException(JsonFormatException::class);
        $mapper->map($json, IntArrayPropClass::class);
    }

    /**
     * @throws JsonFormatException
     */
    public function testMissingConstructor()
    {
        $mapper = new StrictJson();
        $json = '{"does not": "mattter"}';
        $this->expectException(InvalidConfigurationException::class);
        $classname = MissingConstructorClass::class;
        $this->expectExceptionMessage("Type $classname does not have a valid constructor");
        $mapper->map($json, MissingConstructorClass::class);
    }

    public function invalidAdapterProvider()
    {
        return [
            'Adapter with no fromJson method' => [
                new AdapterWithoutFromJson(),
                'Adapter Burba\StrictJson\Fixtures\AdapterWithoutFromJson has no fromJson method',
            ],
            'Adapter with wrong number of arguments' => [
                new AdapterWithWrongNumberOfArguments(),
                "Adapter Burba\StrictJson\Fixtures\AdapterWithWrongNumberOfArguments's fromJson method has the wrong number of parameters, needs exactly 2'",
            ],
            'Adapter that is secretly a number' => [
                2,
                'Adapter of type "integer" is not a valid class',
            ],
            'Adapter than throws a runtime exception' => [
                new AdapterThatThrowsRuntimeException(),
                "Adapter Burba\StrictJson\Fixtures\AdapterThatThrowsRuntimeException threw an exception",
            ],
        ];
    }
}
