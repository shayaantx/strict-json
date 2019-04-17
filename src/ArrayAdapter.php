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
     * @param JsonContext $context
     * @return array
     *
     * @throws JsonFormatException
     */
    public function fromJson($items, StrictJson $delegate, JsonContext $context)
    {
        $mapped_items = [];
        foreach ($items as $idx => $item) {
            $mapped_items[] = $delegate->mapDecoded($item, $this->type, $context->withArrayIndex($idx));
        }
        return $mapped_items;
    }

    public function fromTypes(): array
    {
        return [Type::array()];
    }
}
