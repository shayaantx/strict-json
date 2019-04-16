<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Docs;

use Burba\StrictJson\Adapter;
use Burba\StrictJson\JsonContext;
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Type;
use DateTime;

class DateAdapter implements Adapter
{
    public function fromJson($decoded_json, StrictJson $delegate, JsonContext $context): DateTime
    {
        return DateTime::createFromFormat(DateTime::ISO8601, $decoded_json);
    }

    /**
     * @return Type[]
     */
    public function fromTypes(): array
    {
        return [Type::string()];
    }
}
