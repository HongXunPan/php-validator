<?php

namespace HongXunPan\Validator\Rule\Argument;

class FormatStringArgument
{
    /**
     * @var string
     */
    private $format;

    /**
     * @param string $format
     */
    public function __construct($format)
    {
        $this->format = (string)$format;
    }

    /**
     * @return string
     */
    public function format()
    {
        return $this->format;
    }
}
