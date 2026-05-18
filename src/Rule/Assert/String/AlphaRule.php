<?php

namespace HongXunPan\Validator\Rule\Assert\String;

class AlphaRule extends AbstractStringContentRule
{
    const KEY = 'alpha';
    const MESSAGE = '$paramName must contain letters only';

    protected static function matches($value)
    {
        return preg_match('/^[A-Za-z]+$/', $value) === 1;
    }
}
