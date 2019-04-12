<?php namespace Burba\StrictJson;

class StrictJsonBuilder
{
    private $parameter_adapters = [];
    private $class_adapters = [];

    public function addParameterAdapter(string $class_name, string $parameter_name, object $adapter): self
    {
        if (!isset($this->parameter_adapters[$class_name])) {
            $this->parameter_adapters[$class_name] = [];
        }

        $this->parameter_adapters[$class_name][$parameter_name] = $adapter;
        return $this;
    }

    public function addParameterArrayAdapter(string $class_name, string $parameter_name, string $array_item_type): self
    {
        return $this->addParameterAdapter($class_name, $parameter_name, new ArrayAdapter($array_item_type));
    }

    public function addClassAdapter(string $class_name, object $adapter): self
    {
        $this->class_adapters[$class_name] = $adapter;
        return $this;
    }

    public function build(): StrictJson
    {
        return new StrictJson($this->class_adapters, $this->parameter_adapters);
    }
}
