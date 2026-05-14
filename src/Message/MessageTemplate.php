<?php

namespace HongXunPan\Validator\Message;

/**
 * MessageTemplate 承接默认文案模板。
 */
class MessageTemplate
{
    private $value;

    public function __construct($value)
    {
        $this->value = (string)$value;
    }

    public static function of($value)
    {
        return new static($value);
    }

    public function value()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
