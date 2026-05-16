<?php

namespace HongXunPan\Validator\Internal\Input;

class DeclaredTargetTree
{
    /**
     * @var array
     */
    private $tree;

    public function __construct(array $tree)
    {
        $this->tree = $tree;
    }

    public function toArray()
    {
        return $this->tree;
    }
}
