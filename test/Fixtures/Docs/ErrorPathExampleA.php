<?php declare(strict_types=1);

namespace Burba\StrictJson\Fixtures\Docs;

class ErrorPathExampleA
{
    /** @var array */
    private $b;

    public function __construct(array $b)
    {
        $this->b = $b;
    }
}
