<?php

namespace HongXunPan\Validator\Rule\Assert\String;

class UppercaseRule extends AbstractStringContentRule
{
    const KEY = 'uppercase';
    const MESSAGE = '$paramName must be uppercase';

    protected static function matches($value)
    {
        return static::isAscii($value) && $value === strtoupper($value);
    }
}
