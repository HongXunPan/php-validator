<?php

namespace HongXunPan\Validator\Rule;

use HongXunPan\Validator\Internal\Field\PathLabelMap;
use LogicException;

abstract class AbstractRule implements RuleInterface
{
    const KEY = '';
    const MESSAGE = '$paramName validate failed';

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

    public static function defaultMessage()
    {
        return static::MESSAGE;
    }

    public static function displayRuleValue($rawArg, PathLabelMap $pathLabelMap)
    {
        return (string)$rawArg;
    }
}
