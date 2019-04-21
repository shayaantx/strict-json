<?php declare(strict_types=1);

namespace Burba\StrictJson\Unit;

use Burba\StrictJson\Internal\TypedParameter;
use Burba\StrictJson\Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TypedParameterTest extends TestCase
{
    public function testNoDefaultValue()
    {
        $param = new TypedParameter(Type::int(), TypedParameter::noDefaultValue(), null);
        $this->assertFalse($param->hasDefaultValue());
    }

    public function testHasNullDefaultValue()
    {
        $param = new TypedParameter(Type::int(), null, null);
        $this->assertTrue($param->hasDefaultValue());
        $this->assertNull($param->getDefaultValue());
    }

    public function testGetDefaultValueWhenNoneExists()
    {
        $param = new TypedParameter(Type::int(), TypedParameter::noDefaultValue(), null);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Called getDefaultValue on TypedParameter with no default value');
        $param->getDefaultValue();
    }
}
