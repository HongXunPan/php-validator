<?php

namespace HongXunPan\Validator\Rule\Assert\String;

class AsciiRule extends AbstractStringContentRule
{
    const KEY = 'ascii';
    const MESSAGE = '$paramName must contain ASCII characters only';

    protected static function matches($value)
    {
        return static::isAscii($value);
    }
}
