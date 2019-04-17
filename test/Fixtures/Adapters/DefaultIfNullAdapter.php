<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Adapters;

use Burba\StrictJson\Adapter;
use Burba\StrictJson\JsonContext;
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Type;

/**
 * Don't actually do something like this in real code, just use default values for your constructor parameters!
 */
class DefaultIfNullAdapter implements Adapter
{
    /** @var float */
    private $default_value;

    public function __construct(float $default_value)
    {
        $this->default_value = $default_value;
    }

    public function fromJson($decoded_json, StrictJson $delegate, JsonContext $context): float
    {
        return $decoded_json === null ? $this->default_value : $decoded_json;
    }

    /** @return Type[] */
    public function fromTypes(): array
    {
        return [Type::float()->asNullable()];
    }
}
