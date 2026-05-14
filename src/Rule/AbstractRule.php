<?php

namespace HongXunPan\Validator\Rule;

use LogicException;

abstract class AbstractRule implements RuleInterface
{
    const KEY = '';

    final public static function key()
    {
        if (static::KEY === '') {
            throw new LogicException(get_called_class() . ' 必须覆盖 KEY');
        }

        return static::KEY;
    }

    final public static function of($arg)
    {
        return static::key() . ':' . $arg;
    }
}
