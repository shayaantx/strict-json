<?php namespace Burba\StrictJson;

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use RuntimeException;

class StrictJson
{
	private const BASIC_TYPES = [
		'int',
		'bool',
		'string',
		'float',
	];

	/** @var string[string[object]] */
	private $property_adapters;
	/** @var array */
	private $class_adapters;

	public function __construct(array $class_adapters = [], array $property_adapters = [])
	{
		$this->property_adapters = $property_adapters;
		$this->class_adapters = $class_adapters;
	}

	public static function builder()
	{
		return new StrictJsonBuilder();
	}

	/**
	 * @param string $json
	 * @param string $class
	 *
	 * @return object
	 * @throws JsonFormatException
	 */
	public function map(string $json, string $class)
	{
		$parsed = json_decode($json, true);
		if (json_last_error()) {
			$err = json_last_error_msg();
			throw new JsonFormatException(
				"Invalid json $err: $json"
			);
		}

		return $this->mapParsed($parsed, $class);
	}

	/** @noinspection PhpDocMissingThrowsInspection */
	/**
	 * @param array|string|int|bool $parsed_json
	 * @param string $target_type
	 *
	 * @return mixed
	 * @throws JsonFormatException
	 */
	public function mapParsed($parsed_json, string $target_type)
	{
		$adapter = $this->class_adapters[$target_type] ?? null;
		if ($adapter !== null) {
			return $this->mapWithAdapter($adapter, $parsed_json);
		}

		$normalized_target_type = $this->normalize($target_type);
		if (in_array($normalized_target_type, self::BASIC_TYPES)) {
			$json_type = $this->normalize(gettype($parsed_json));
			if ($json_type === $normalized_target_type) {
				return $parsed_json;
			} else {
				throw new JsonFormatException("Value is of type $json_type, expected type $normalized_target_type");
			}
		}

		try {
			$refl_class = new ReflectionClass($target_type);
		} catch (ReflectionException $e) {
			throw new RuntimeException($e);
		}

		$constructor = $refl_class->getConstructor();
		$parameters = $constructor->getParameters();
		$constructor_args = [];
		foreach ($parameters as $parameter) {
			$parameter_name = $parameter->getName();
			if (isset($parsed_json[$parameter_name])) {
				$value = $parsed_json[$parameter_name];
			} else if ($parameter->isDefaultValueAvailable()) {
				// Guaranteed not to throw because we checked in the if condition
				/** @noinspection PhpUnhandledExceptionInspection */
				$value = $parameter->getDefaultValue();
			} else {
				throw new JsonFormatException("Value is missing field named $parameter_name");
			}

			$adapter = $this->property_adapters[$target_type][$parameter_name] ?? null;

			if ($adapter !== null) {
				try {
					$value = $this->mapWithAdapter($adapter, $value);
				} catch (InvalidConfigurationException $e) {
					throw new InvalidConfigurationException("Adapter for parameter $target_type::$parameter_name has the following issues");
				}
			}

			$adapter = $this->class_adapters[$target_type] ?? null;
			if ($adapter !== null) {
				try {
					$value = $this->mapWithAdapter($adapter, $value);
				} catch (InvalidConfigurationException $e) {
					throw new InvalidConfigurationException("Adapter for parameter $target_type has the following issues");
				}
			}

			if (!$parameter->getType()->isBuiltin() && $value !== null) {
				$value = $this->mapParsed($value, $parameter->getType()->getName());
			}

			$this->requireCompatibleTypes($parameter, $value);
			$constructor_args[] = $value;
		}

		return $refl_class->newInstanceArgs($constructor_args);
	}

	/**
	 * @param $adapter
	 * @param $value
	 * @return mixed
	 * @throws JsonFormatException
	 */
	private function mapWithAdapter($adapter, $value)
	{
		/** @noinspection PhpUnhandledExceptionInspection */
		$adapter_class = new ReflectionClass($adapter);
		try {
			$adapter_method = $adapter_class->getMethod('fromJson');
		} catch (ReflectionException $e) {
			throw new InvalidConfigurationException(
				"Adapter {$adapter_class->getName()} has no fromJson method",
				0,
				$e
			);
		}

		$adapter_method_params = $adapter_method->getParameters();
		if (count($adapter_method_params) !== 2) {
			throw new InvalidConfigurationException(
				"Adapter {$adapter_class->getName()}'s fromJson method has the wrong number of parameters, needs exactly 2'"
			);
		}

		$this->requireCompatibleTypes($adapter_method_params[1], $value);
		try {
			return $adapter_method->invoke($adapter, $this, $value);
		} catch (Exception $e) { // Catch all exceptions so we can re-throw with all the json info
			throw new JsonFormatException(
				"Adapter {$adapter_class->getName()} threw an exception",
				0,
				$e
			);
		}
	}

	/**
	 * @param ReflectionParameter $parameter
	 * @param $value
	 * @throws JsonFormatException
	 */
	private function requireCompatibleTypes(ReflectionParameter $parameter, $value): void
	{
		if (!$this->typesAreCompatible($parameter, $value)) {
			$json_type = gettype($value);
			throw new JsonFormatException(
				"{$parameter->getName()} has type {$parameter->getType()} in class but has type $json_type in json"
			);
		}
	}

	private function typesAreCompatible(ReflectionParameter $parameter, $json_value)
	{
		$parameter_type_name = $this->normalize($parameter->getType()->getName());
		$json_type_name = $this->normalize(gettype($json_value));

		if ($parameter_type_name === $json_type_name) {
			return true;
		} else if ($parameter->allowsNull() && $json_type_name === 'NULL') {
			return true;
		} else if ($json_value !== null && $parameter->getType()->getName() == get_class($json_value)) {
			return true;
		} else {
			return false;
		}
	}

	/** @noinspection PhpDocMissingThrowsInspection */

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
}