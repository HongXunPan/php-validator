<?php

namespace HongXunPan\Validator\Context;

class PathValue
{
    /**
     * @var bool
     */
    private $exists;
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param bool $exists
     * @param mixed $value
     */
    public function __construct($exists, $value)
    {
        $this->exists = (bool)$exists;
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->exists;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }
}
