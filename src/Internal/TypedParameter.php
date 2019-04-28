<?php declare(strict_types=1);

namespace Burba\StrictJson\Internal;

use Burba\StrictJson\Adapter;
use Burba\StrictJson\Type;
use RuntimeException;
use stdClass;

/**
 * This class is not subject to semantic versioning compatibility guarantees
 */
class TypedParameter
{
    /** @var stdClass */
    private static $no_default_value;
    /** @var Type */
    private $type;
    /** @var mixed */
    private $default_value;
    /** @var Adapter|null */
    private $adapter;

    /** @return stdClass */
    public static function noDefaultValue()
    {
        if (self::$no_default_value === null) {
            self::$no_default_value = new stdClass();
        }
        return self::$no_default_value;
    }

    /**
     * @param Type $type
     * @param mixed $default_value
     * @param Adapter|null $adapter
     */
    public function __construct(Type $type, $default_value, ?Adapter $adapter)
    {
        $this->type = $type;
        $this->default_value = $default_value;
        $this->adapter = $adapter;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function hasDefaultValue(): bool
    {
        return $this->default_value !== self::noDefaultValue();
    }

    /** @return mixed */
    public function getDefaultValue()
    {
        if (!$this->hasDefaultValue()) {
            throw new RuntimeException('Called getDefaultValue on TypedParameter with no default value');
        }
        return $this->default_value;
    }

    public function getAdapter(): ?Adapter
    {
        return $this->adapter;
    }
}
