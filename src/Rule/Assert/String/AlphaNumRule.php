<?php

namespace HongXunPan\Validator\Rule\Assert\String;

class AlphaNumRule extends AbstractStringContentRule
{
    const KEY = 'alphaNum';
    const MESSAGE = '$paramName must contain letters and numbers only';

    protected static function matches($value)
    {
        return preg_match('/^[A-Za-z0-9]+$/', $value) === 1;
    }
}
