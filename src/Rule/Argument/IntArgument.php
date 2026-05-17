<?php

namespace HongXunPan\Validator\Rule\Argument;

class IntArgument
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct($value)
    {
        $this->value = (int)$value;
    }

    /**
     * @return int
     */
    public function value()
    {
        return $this->value;
    }
}
