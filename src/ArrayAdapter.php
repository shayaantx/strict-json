<?php namespace Burba\StrictJson;

/**
 * Json Adapter that maps json arrays to items of the given type
 * @internal
 */
class ArrayAdapter
{
    /** @var string */
    private $item_type;

    /**
     * @param string $item_type Either a class name or a scalar name (i.e. string, int, float, bool)
     */
    public function __construct(string $item_type)
    {
        $this->item_type = $item_type;
    }

    /**
     * Turn given array of items into an array of items of the type provided in the constructor
     *
     * @param StrictJson $delegate
     * @param array $items
     * @param JsonContext|null $context
     *
     * @return array
     * @throws JsonFormatException If the array of items cannot be transformed
     */
    public function fromJson(StrictJson $delegate, array $items, ?JsonContext $context = null): array
    {
        $context = $context ?? JsonContext::root();
        $mapped_items = [];
        foreach ($items as $idx => $item) {
            $mapped_items[] = $delegate->mapDecoded($item, $this->item_type, $context->withArrayIndex($idx));
        }
        return $mapped_items;
    }
}
