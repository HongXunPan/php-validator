<?php

namespace HongXunPan\Validator\Rule\Assert\String;

class LowercaseRule extends AbstractStringContentRule
{
    const KEY = 'lowercase';
    const MESSAGE = '$paramName must be lowercase';

    protected static function matches($value)
    {
        return static::isAscii($value) && $value === strtolower($value);
    }
}
