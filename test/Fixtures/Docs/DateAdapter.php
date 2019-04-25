<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Docs;

use Burba\StrictJson\Adapter;
use Burba\StrictJson\Internal\ArrayAdapter;
use Burba\StrictJson\JsonFormatException;
use Burba\StrictJson\JsonPath;
use Burba\StrictJson\StrictJson;
use Burba\StrictJson\Type;
use DateTime;

class DateAdapter implements Adapter
{
    /**
     * Convert decoded json into the specified type
     *
     * @param string $decoded_json This is guaranteed to be one of the types returned from fromTypes
     * @param StrictJson $delegate Use this if you want to delegate a portion of the decoding process to StrictJson
     * @param JsonPath $path Include it when you throw JsonFormatException or delegate to StrictJson for better error
     * messages
     *
     * @return DateTime
     * @throws JsonFormatException If the JSON is not in the format you expect
     *
     * @see ArrayAdapter For a more advanced example that uses delegation and paths
     */
    public function fromJson($decoded_json, StrictJson $delegate, JsonPath $path): DateTime
    {
        $date = DateTime::createFromFormat(DATE_ISO8601, $decoded_json);
        if ($date === false) {
            throw new JsonFormatException("Expected ISO8601 date, found $decoded_json", $path);
        }

        return $date;
    }

    /**
     * @return Type[]
     */
    public function fromTypes(): array
    {
        return [Type::string()];
    }
}
