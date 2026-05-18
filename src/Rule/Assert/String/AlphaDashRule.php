<?php

namespace HongXunPan\Validator\Rule\Assert\String;

class AlphaDashRule extends AbstractStringContentRule
{
    const KEY = 'alphaDash';
    const MESSAGE = '$paramName must contain letters, numbers, dashes and underscores only';

    protected static function matches($value)
    {
        return preg_match('/^[A-Za-z0-9_-]+$/', $value) === 1;
    }
}
