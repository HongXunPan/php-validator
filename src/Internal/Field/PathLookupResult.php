<?php

namespace HongXunPan\Validator\Internal\Field;

class PathLookupResult
{
    /**
     * @var bool
     */
    private $exists;
    /**
     * @var mixed
     */
    private $value;

    public function __construct($exists, $value)
    {
        $this->exists = (bool)$exists;
        $this->value = $value;
    }

    public function exists()
    {
        return $this->exists;
    }

    public function value()
    {
        return $this->value;
    }
}
