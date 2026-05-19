<?php

namespace HongXunPan\Validator\Rule;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Rule\Argument\NoArgumentParser;
use LogicException;

abstract class AbstractRule implements RuleInterface
{
    const KEY = '';
    const MESSAGE = '$paramName validate failed';
    const ARGUMENT_PARSER = NoArgumentParser::class;

    /**
     * @return string
     */
    final public static function key()
    {
        if (static::KEY === '') {
            throw new LogicException(get_called_class() . ' 必须覆盖 KEY');
        }

        return static::KEY;
    }

    /**
     * @param mixed $arg
     *
     * @return string
     */
    final public static function of($arg)
    {
        return static::key() . ':' . $arg;
    }

    /**
     * @param mixed $arg
     *
     * @return string
     */
    final public static function ofJson($arg)
    {
        $encoded = json_encode($arg, JSON_UNESCAPED_UNICODE);
        if ($encoded === false) {
            throw new LogicException(get_called_class() . ' 的 JSON 规则参数编码失败：' . json_last_error_msg());
        }

        return static::of($encoded);
    }

    /**
     * @return string
     */
    public static function defaultMessage()
    {
        return static::MESSAGE;
    }

    /**
     * @return string
     */
    public static function argumentParserClass()
    {
        return static::ARGUMENT_PARSER;
    }

    /**
     * @param mixed $rawArg
     *
     * @return string
     */
    public static function displayRuleValue($rawArg, PathLabelMap $pathLabelMap)
    {
        return (string)$rawArg;
    }
}
