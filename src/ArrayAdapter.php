<?php namespace Burba\StrictJson;

/**
 * Json Adapter that maps json arrays to items of the given type
 */
class ArrayAdapter
{
	/** @var string */
	private $item_type;

	public function __construct(string $item_type)
	{
		$this->item_type = $item_type;
	}

	/**
	 * @param StrictJson $delegate
	 * @param array $items
	 * @return array
	 * @throws JsonFormatException
	 */
	public function fromJson(StrictJson $delegate, array $items): array
	{
		$mapped_items = [];
		$index = 0;
		try {
			foreach ($items as $item) {
				$mapped_items[] = $delegate->mapParsed($item, $this->item_type);
				$index++;
			}
		} catch (JsonFormatException $e) {
			throw new JsonFormatException("Unable to map item $index", 0, $e);
		}
		return $mapped_items;
	}
}