<?php namespace Burba\StrictJson;

/**
 * Represents the current state of JSON decoding. Used to identify where in the JSON errors occurred
 */
class JsonContext
{
    private $context;

    private function __construct(string $context = '$')
    {
        $this->context = $context;
    }

    public static function root()
    {
        return new JsonContext();
    }

    /**
     * @param int $index The array index
     * @return JsonContext A new JsonContext that represents indexing into the array of the current context
     */
    public function withArrayIndex(int $index)
    {
        return new JsonContext($this->context . "[$index]");
    }

    /**
     * @param string $property_name
     * @return JsonContext A new JsonContext that represents accessing a property of the current context
     */
    public function withProperty(string $property_name)
    {
        return new JsonContext($this->context . ".$property_name");
    }

    public function __toString()
    {
        return $this->context == '$' ? '<json_root>' : $this->context;
    }
}
