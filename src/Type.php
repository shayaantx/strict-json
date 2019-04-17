<?php declare(strict_types=1);

namespace Burba\StrictJson;

use ReflectionParameter;

/**
 * Represents a PHP type
 */
class Type
{
    private const SCALAR_TYPES = [
        'int',
        'bool',
        'string',
        'float',
    ];

    /** @var string */
    private $typename;
    /** @var bool */
    private $nullable;

    private function __construct(string $typename, bool $nullable = false)
    {
        $this->typename = $typename;
        $this->nullable = $nullable;
    }

    public static function int(): Type
    {
        return new Type('int');
    }

    public static function float(): Type
    {
        return new Type('float');
    }

    public static function string(): Type
    {
        return new Type('string');
    }

    public static function array(): Type
    {
        return new Type('array');
    }

    public static function bool(): Type
    {
        return new Type('bool');
    }

    /**
     * @param ReflectionParameter $parameter
     * @param JsonContext $context
     * @return Type
     * @internal
     */
    public static function from(ReflectionParameter $parameter, JsonContext $context): Type
    {
        $parameter_type = $parameter->getType()->getName();
        if ($parameter->isArray()) {
            return new Type('array', $parameter->allowsNull());
        } elseif (in_array($parameter_type, self::SCALAR_TYPES)) {
            return new Type($parameter_type, $parameter->allowsNull());
        } elseif (class_exists($parameter_type)) {
            return new Type($parameter_type, $parameter->allowsNull());
        } else {
            throw new InvalidConfigurationException("Unsupported type $parameter_type", $context);
        }
    }

    /**
     * Create a Type from the given class
     *
     * @param string $class
     * @return Type
     */
    public static function ofClass(string $class): Type
    {
        if (!class_exists($class)) {
            throw new InvalidConfigurationException("Type \"$class\" is not a valid class", JsonContext::root());
        }
        return new Type($class, false);
    }

    /**
     * @return string
     * @internal
     */
    public function getTypeName(): string
    {
        return $this->typename;
    }

    /**
     * Create a nullable copy of this type
     * @return Type
     */
    public function asNullable(): Type
    {
        return new Type($this->typename, true);
    }

    /**
     * @return bool
     * @internal
     */
    public function isScalar()
    {
        return in_array($this->typename, self::SCALAR_TYPES);
    }

    /**
     * @return bool
     * @internal
     */
    public function isClass()
    {
        return class_exists($this->typename);
    }

    /**
     * @return bool
     * @internal
     */
    public function isArray()
    {
        return $this->typename === 'array';
    }

    /**
     * @param mixed $value The proposed value
     * @return bool True if a parameter of this type would allow the given value, false otherwise
     * @internal
     */
    public function allowsValue($value): bool
    {
        $value_type = $this->normalize(gettype($value));

        return $this->nullable && $value === null
            || $this->getTypeName() === $value_type
            || (is_object($value) && get_class($value) === $this->getTypeName());
    }

    /**
     * The "Reflection" apis and gettype return primitive names slightly differently, this forces them all to be the
     * same so you can compare them
     *
     * @param string $primitive_type_name
     *
     * @return string
     */
    private function normalize(string $primitive_type_name)
    {
        switch ($primitive_type_name) {
            case 'int':
            case 'integer':
                return 'int';
            case 'double':
            case 'float':
                return 'float';
            case 'bool':
            case 'boolean':
                return 'bool';
            default:
                return $primitive_type_name;
        }
    }

    public function __toString(): string
    {
        return $this->nullable ? '?' . $this->getTypeName() : $this->getTypeName();
    }
}
