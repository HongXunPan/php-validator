<?php

namespace HongXunPan\Validator\Internal\Input;

class DeclaredTargetTree
{
    /**
     * @var array
     */
    private $tree;

    /**
     * @param array<string, mixed> $tree
     */
    public function __construct(array $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return $this->tree;
    }
}
