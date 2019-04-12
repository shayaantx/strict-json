<?php namespace Burba\StrictJson;

use Burba\StrictJson\Fixtures\AdapterThatThrowsJsonFormatException;
use Burba\StrictJson\Fixtures\AdapterThatThrowsRuntimeException;
use Burba\StrictJson\Fixtures\AdapterWithoutFromJson;
use Burba\StrictJson\Fixtures\AdapterWithWrongNumberOfArguments;
use Burba\StrictJson\Fixtures\BasicClass;
use Burba\StrictJson\Fixtures\HasClassProp;
use Burba\StrictJson\Fixtures\Example\User;
use Burba\StrictJson\Fixtures\HasIntArrayProp;
use Burba\StrictJson\Fixtures\HasIntProp;
use Burba\StrictJson\Fixtures\IntPropClassAdapterThatAddsFour;
use Burba\StrictJson\Fixtures\MissingConstructor;
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
                new HasIntProp(5)
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
                HasIntArrayProp::class,
                'int_array_prop',
                new ArrayAdapter('int')
            )
            ->build();

        $this->assertEquals(
            new HasIntArrayProp([1, 2, 3]),
            $mapper->map($json, HasIntArrayProp::class)
        );
    }

    /**
     * @throws JsonFormatException
     */
    public function testClassAdapterForRootObject()
    {
        $mapper = StrictJson::builder()
            ->addClassAdapter(HasIntProp::class, new IntPropClassAdapterThatAddsFour())
            ->build();

        $json = '{ "int_prop": 1 }';
        $this->assertEquals(
            new HasIntProp(5),
            $mapper->map($json, HasIntProp::class)
        );
    }

    /**
     * @throws JsonFormatException
     */
    public function testClassAdapterForProperty()
    {
        $mapper = StrictJson::builder()
            ->addClassAdapter(HasIntProp::class, new IntPropClassAdapterThatAddsFour())
            ->build();

        $json = '{ "int_prop_class": { "int_prop": 1 } }';
        $this->assertEquals(
            new HasClassProp(new HasIntProp(5)),
            $mapper->map($json, HasClassProp::class)
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
        $mapper = new StrictJson([HasIntProp::class => $adapter]);
        $json = '{"does_not": "matter"}';
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($expected_exception_message);
        $mapper->map($json, HasIntProp::class);
    }

    /**
     * Verify that passing invalid values in for parameter adapters always throws a config exception
     *
     * @throws JsonFormatException
     * @dataProvider invalidAdapterProvider
     */
    public function testInvalidParameterAdapter($adapter)
    {
        $mapper = new StrictJson([], [HasIntProp::class => ['int_prop' => $adapter]]);
        $json = '{ "int_prop": 1 }';
        $this->expectException(InvalidConfigurationException::class);
        $mapper->map($json, HasIntProp::class);
    }

    /**
     * @throws JsonFormatException
     */
    public function testClassAdapterThatThrowsJsonFormatException()
    {
        $mapper = new StrictJson([HasIntProp::class => new AdapterThatThrowsJsonFormatException()]);
        $json = '{"does_not": "matter"}';
        $this->expectException(JsonFormatException::class);
        $mapper->map($json, HasIntProp::class);
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
        $mapper->map($json, HasIntProp::class);
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
        $mapper->map($json, HasIntProp::class);
    }

    /**
     * @throws JsonFormatException
     */
    public function testJsonHasWrongItemType()
    {
        $mapper = StrictJson::builder()
            ->addParameterArrayAdapter(HasIntArrayProp::class, 'int_array_prop', 'int')
            ->build();
        $json = '{"int_array_prop": [1, "2", 3]}';
        $this->expectException(JsonFormatException::class);
        $mapper->map($json, HasIntArrayProp::class);
    }

    /**
     * @throws JsonFormatException
     */
    public function testMissingConstructor()
    {
        $mapper = new StrictJson();
        $json = '{"does not": "mattter"}';
        $this->expectException(InvalidConfigurationException::class);
        $classname = MissingConstructor::class;
        $this->expectExceptionMessage("Type $classname does not have a valid constructor");
        $mapper->map($json, MissingConstructor::class);
    }

    /**
     * Verify that StrictJson throws an exception when an Adapter specifies a type but the JSON type doesn't match
     *
     * @throws JsonFormatException
     */
    public function testMismatchedAdapterParameterJsonField()
    {
        $mapper = new StrictJson([HasIntProp::class => new IntPropClassAdapterThatAddsFour()]);
        $json = '{"int_prop_class": 4}';
        $this->expectException(JsonFormatException::class);
        $this->expectExceptionMessage('Parameter "parsed_json" has type "array" in class but has type "integer" in JSON');
        $mapper->map($json, HasClassProp::class);
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
