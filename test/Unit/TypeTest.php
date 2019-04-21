<?php
declare(strict_types=1);

namespace Burba\StrictJson\Unit;

use Burba\StrictJson\Fixtures\HasIntProp;
use Burba\StrictJson\Fixtures\HasNullableProp;
use Burba\StrictJson\Type;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testAllowsMatchingTypes()
    {
        // Set up cases inside test method so that static factory functions count as covered
        // Code that runs inside a dataprovider doesn't count towards coverage, and therefore infection doesn't know
        // that it's a covering test
        $cases = [
            'int with int' => [Type::int(), 1],
            'float with float' => [Type::float(), 1.0],
            'array with array' => [Type::array(), []],
            'bool with bool' => [Type::bool(), true],
            'Class with matching Class' => [Type::ofClass(HasIntProp::class), new HasIntProp(1)],
            'nullable scalar with null' => [Type::int()->asNullable(), null],
            'nullable class with null' => [Type::ofClass(HasIntProp::class)->asNullable(), null],
        ];

        foreach ($cases as $name => $case) {
            $this->assertTrue($case[0]->allowsValue($case[1]), "Expected $name to be allowed");
        }
    }

    public function testDoesNotAllowNonMatchingTypes()
    {
        // Set up cases inside test method so that static factory functions count as covered
        // Code that runs inside a dataprovider doesn't count towards coverage, and therefore infection doesn't know
        // that it's a covering test
        $cases = [
            'int with float' => [Type::int(), 1.7],
            'Class with non matching Class' => [Type::ofClass(HasNullableProp::class), new HasIntProp(4)],
            'non-null scalar with null' => [Type::float(), null],
            'non-null class with null' => [Type::ofClass(HasNullableProp::class), null],
        ];

        foreach ($cases as $name => $case) {
            $this->assertFalse($case[0]->allowsValue($case[1]), "Expected $name not to be allowed");
        }
    }
}
