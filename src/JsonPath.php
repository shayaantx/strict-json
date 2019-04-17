<?php declare(strict_types=1);

namespace Burba\StrictJson;

/**
 * Represents the current state of JSON decoding. Used to identify where in the JSON errors occurred
 */
class JsonPath
{
    private $path;

    private function __construct(string $path = '$')
    {
        $this->path = $path;
    }

    /**
     * A JsonPath at the root of the JSON
     * @return JsonPath
     */
    public static function root()
    {
        return new JsonPath();
    }

    /**
     * @param int $index The array index
     * @return JsonPath A new JsonPath that represents indexing into the array of the current path
     */
    public function withArrayIndex(int $index)
    {
        return new JsonPath($this->path . "[$index]");
    }

    /**
     * @param string $property_name
     * @return JsonPath A new JsonPath that represents accessing a property of the current path
     */
    public function withProperty(string $property_name)
    {
        return new JsonPath($this->path . ".$property_name");
    }

    public function __toString()
    {
        return $this->path == '$' ? '<json_root>' : $this->path;
    }
}
