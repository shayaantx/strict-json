<?php
declare(strict_types=1);

namespace Burba\StrictJson;

use Burba\StrictJson\Fixtures\HasIntProp;
use Burba\StrictJson\Fixtures\HasNullableProp;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    /**
     * @param Type $type
     * @param $value
     * @dataProvider validTypeValuesProvider
     */
    public function testAllowsMatchingTypes(Type $type, $value)
    {
        $this->assertTrue($type->allowsValue($value));
    }

    /**
     * @param Type $type
     * @param $value
     * @dataProvider invalidTypeValuesProvider
     */
    public function testDoesNotAllowNonMatchingTypes(Type $type, $value)
    {
        $this->assertFalse($type->allowsValue($value));
    }

    public function invalidTypeValuesProvider()
    {
        return [
            'int with float' => [Type::int(), 1.7],
            'Class with non matching Class' => [Type::ofClass(HasNullableProp::class), new HasIntProp(4)],
            'non-null scalar with null' => [Type::float(), null],
            'non-null class with null' => [Type::ofClass(HasNullableProp::class), null],
        ];
    }

    public function validTypeValuesProvider()
    {
        return [
            'int with int' => [Type::int(), 1],
            'float with float' => [Type::float(), 1.0],
            'array with array' => [Type::array(), []],
            'bool with bool' => [Type::bool(), true],
            'Class with matching Class' => [Type::ofClass(HasNullableProp::class), new HasNullableProp(null)],
            'nullable scalar with null' => [Type::int()->asNullable(), null]
        ];
    }
}
