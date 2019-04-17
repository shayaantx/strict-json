<?php declare(strict_types=1);

namespace Burba\StrictJson;

/**
 * Adapter that converts a decoded JSON array to an array of items of the specified type
 * @internal
 */
class ArrayAdapter implements Adapter
{
    /** @var Type */
    private $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    /**
     * @param array $items
     * @param StrictJson $delegate
     * @param JsonPath $path
     * @return array
     *
     * @throws JsonFormatException
     */
    public function fromJson($items, StrictJson $delegate, JsonPath $path)
    {
        $mapped_items = [];
        foreach ($items as $idx => $item) {
            $mapped_items[] = $delegate->mapDecoded($item, $this->type, $path->withArrayIndex($idx));
        }
        return $mapped_items;
    }

    public function fromTypes(): array
    {
        return [Type::array()];
    }
}
