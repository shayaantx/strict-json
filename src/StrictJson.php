<?php declare(strict_types=1);

namespace Burba\StrictJson;

use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

class StrictJson
{
    /** @var string[string[object]] */
    private $parameter_adapters;
    /** @var array */
    private $type_adapters;

    /**
     * Create an instance of StrictJson configured with type and parameter adapters.
     *
     * If you're doing more than very simple configuration, you probably want to use the static build method, which
     * gives you a nicer fluent interface for configuration
     *
     * @param array $type_adapters A mapping of string type names to adapter objects. The type names can be either full
     * class names (including the namespace) or names of primitives if you want to change the way all primitives are
     * mapped
     * @param array $parameter_adapters A mapping of string type names to associative arrays, which map parameter names
     * to adapters. If you're configuring these, you probably want to use the StrictJson::builder() method instead
     *
     * @see StrictJson::builder()
     */
    public function __construct(array $type_adapters = [], array $parameter_adapters = [])
    {
        $this->parameter_adapters = $parameter_adapters;
        $this->type_adapters = $type_adapters;
    }

    /**
     * Create a builder for this class for advanced fluent configuration
     *
     * @return StrictJsonBuilder
     */
    public static function builder(): StrictJsonBuilder
    {
        return new StrictJsonBuilder();
    }

    /**
     * Convert the given json into an instance of the given class
     *
     * @param string $json The json string to convert
     * @param string|Type $type Either the class or a Type to map to
     *
     * @return object The parsed JSON in the type of $class
     *
     * @throws JsonFormatException If $json is not valid JSON, or if the constructor for $class has parameters that do
     * not match in name AND type to the JSON's properties
     */
    public function map(string $json, $type)
    {
        $type = $type instanceof Type ? $type : Type::ofClass($type);
        return $this->mapDecoded($this->safeDecode($json), $type, JsonContext::root());
    }

    /**
     * Convert a JSON array of items into a PHP array of items of the specified type
     *
     * @param string $json The JSON string
     * @param string|Type $type Either the class or a Type to map each item in the json array to
     *
     * @return mixed An array of mapped items
     *
     * @throws JsonFormatException If any of the items do not match the specified type, or the JSON is invalid
     */
    public function mapToArrayOf(string $json, $type)
    {
        $type = $type instanceof Type ? $type : Type::ofClass($type);
        return $this->mapWithAdapter($this->safeDecode($json), new ArrayAdapter($type), JsonContext::root());
    }

    /**
     * Convert decoded json into an instance of the given target type
     *
     * @param mixed $decoded_json An associative array or other primitive
     * @param Type $target_type Either a class name or a scalar name (i.e. string, int, float, bool)
     * @param JsonContext $context The current parsing context, or null if being called at the root of the decoded JSON
     *
     * @return mixed
     * @throws JsonFormatException
     */
    public function mapDecoded($decoded_json, Type $target_type, JsonContext $context)
    {
        $adapter = $this->type_adapters[$target_type->getTypeName()] ?? null;
        if ($adapter !== null) {
            return $this->mapWithAdapter($decoded_json, $adapter, $context);
        }

        if ($target_type->isScalar()) {
            return $this->mapScalar($decoded_json, $target_type, $context);
        }

        if ($target_type->isClass()) {
            return $this->mapClass($decoded_json, $target_type, $context);
        }

        if ($target_type->isArray()) {
            throw new InvalidConfigurationException("Cannot map to arrays directly, use StrictJson::mapToArrayOf()", $context);
        }

        // It should be impossible to get here, Type should either be a scalar, a class, or an array
        //@codeCoverageIgnoreStart
        throw new InvalidConfigurationException("Target type \"$target_type\" is not a scalar type or valid class and has no registered type adapter", $context);
        //@codeCoverageIgnoreEnd
    }

    /**
     * Map a given decoded json value with the specified adapter
     *
     * @param string|int|array|bool|float $value The decoded json value to adapt
     * @param Adapter $adapter Object with a fromJson method
     * @param JsonContext $context The current decoding context
     *
     * @return mixed Whatever the adapter returns
     * @throws JsonFormatException If the provided value doesn't match the value the adapter expects
     */
    private function mapWithAdapter($value, Adapter $adapter, JsonContext $context)
    {
        $context = $context ?? JsonContext::root();

        $supports_value = false;
        foreach ($adapter->fromTypes() as $supported_type) {
            $supports_value |= $supported_type->allowsValue($value);
        }

        if (!$supports_value) {
            $json_type = gettype($value);
            $num_parameters = count($adapter->fromTypes());
            $adapter_class = get_class($adapter);
            if ($num_parameters > 1) {
                $type_names = [];
                foreach ($adapter->fromTypes() as $type) {
                    $type_names[] = $type->__toString();
                }
                $type_name_list = join(', ', $type_names);
                $expectation = "one of [$type_name_list]";
            } elseif ($num_parameters > 0) {
                $expectation = $adapter->fromTypes()[0]->__toString();
            } else {
                throw new InvalidConfigurationException(
                    "Adapter $adapter_class does not support any types! (fromTypes must return an non-empty array)",
                    $context
                );
            }

            throw new JsonFormatException("Expected $expectation, found $json_type (using $adapter_class)", $context);
        }

        try {
            return $adapter->fromJson($value, $this, $context);
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (JsonFormatException $e) {
            throw $e;
        } catch (Exception $e) {
            $adapter_class = get_class($adapter);
            throw new InvalidConfigurationException("Adapter {$adapter_class} threw an exception", $context, $e);
        }
    }

    /**
     * @param $decoded_json
     * @param Type $target_type
     * @param JsonContext $context
     *
     * @return mixed
     * @throws JsonFormatException
     */
    private function mapScalar($decoded_json, Type $target_type, JsonContext $context)
    {
        if ($target_type->allowsValue($decoded_json)) {
            return $decoded_json;
        } else {
            $json_type = gettype($decoded_json);
            throw new JsonFormatException("Value is of type $json_type, expected type $target_type", $context);
        }
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @param $parsed_json
     * @param Type $type
     * @param JsonContext $context
     *
     * @return object
     * @throws JsonFormatException
     */
    private function mapClass($parsed_json, Type $type, JsonContext $context): object
    {
        try {
            $class = new ReflectionClass($type->getTypeName());
            // @codeCoverageIgnoreStart
        } catch (ReflectionException $e) {
            // Right now it's not possible to trigger this exception from the public API, because the only method that
            // calls this checks if $classname is a class first
            throw new InvalidConfigurationException("Type $type is not a valid class", $context, $e);
            // @codeCoverageIgnoreEnd
        }

        $constructor = $class->getConstructor();
        if ($constructor === null) {
            throw new InvalidConfigurationException("Type $type does not have a valid constructor", $context);
        }
        $parameters = $constructor->getParameters();
        $constructor_args = [];
        foreach ($parameters as $parameter) {
            $parameter_name = $parameter->getName();
            if ($parameter->getType() === null) {
                throw new InvalidConfigurationException("$type::__construct has parameter named $parameter_name with no specified type", $context);
            }

            if (array_key_exists($parameter_name, $parsed_json)) {
                $value = $parsed_json[$parameter_name];
                $adapter = $this->parameter_adapters[$type->getTypeName()][$parameter_name] ?? null;
                $param_context = $context->withProperty($parameter_name);
                $param_type = Type::from($parameter, $param_context);
                if ($adapter !== null) {
                    $value = $this->mapWithAdapter($value, $adapter, $param_context);
                } elseif ($param_type->isArray()) {
                    // Catch the array case here, even though it would be caught in mapDecoded, because we have more
                    // information for a more helpful error message
                    throw new InvalidConfigurationException(
                        "$type::__construct has parameter name $parameter_name of type array with no parameter adapter\n"
                        . "(Use StrictJson::builder()->addArrayParameterAdapter(...) to register an array adapter for this class)",
                        $param_context
                    );
                } else {
                    $value = $this->mapDecoded($value, Type::from($parameter, $param_context), $param_context);
                }
                $constructor_args[] = $value;
            } elseif ($parameter->isDefaultValueAvailable()) {
                // Guaranteed not to throw because we checked in the if condition
                /** @noinspection PhpUnhandledExceptionInspection */
                $constructor_args[] = $parameter->getDefaultValue();
            } else {
                throw new JsonFormatException("$type::__construct has non-optional parameter named $parameter_name that does not exist in JSON", $context);
            }
        }

        try {
            return $class->newInstanceArgs($constructor_args);
        } catch (InvalidArgumentException $e) {
            throw new JsonFormatException("{$type->getTypeName()} threw a validation exception in the constructor", $context, $e);
        } catch (Exception $e) {
            $encoded_args = json_encode($constructor_args);
            throw new InvalidConfigurationException(
                "Unable to construct object of type {$type->getTypeName()} with args $encoded_args",
                $context,
                $e
            );
        }
    }

    /**
     * Decode the specified JSON, throwing if it's invalid JSON
     *
     * @param string $json
     * @return mixed
     *
     * @throws JsonFormatException
     */
    private function safeDecode(string $json)
    {
        $decoded = json_decode($json, true);
        if (json_last_error()) {
            $err = json_last_error_msg();
            throw new JsonFormatException(
                "Unable to parse invalid JSON ($err): $json",
                JsonContext::root()
            );
        }
        return $decoded;
    }
}
