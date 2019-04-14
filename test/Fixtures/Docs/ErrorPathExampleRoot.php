<?php namespace Burba\StrictJson\Fixtures\Docs;

class ErrorPathExampleRoot
{
    /**
     * @var ErrorPathExampleA
     */
    private $a;

    public function __construct(ErrorPathExampleA $a)
    {
        $this->a = $a;
    }
}
