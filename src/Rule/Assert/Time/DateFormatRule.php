<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

use HongXunPan\Validator\Rule\Argument\FormatStringArgument;
use HongXunPan\Validator\Rule\Argument\FormatStringArgumentParser;

class DateFormatRule extends AbstractDateFormatAssertionRule
{
    const KEY = 'dateFormat';
    const MESSAGE = '$paramName must match date format $rule';
    const ARGUMENT_PARSER = FormatStringArgumentParser::class;

    protected static function format($argument)
    {
        return $argument instanceof FormatStringArgument
            ? $argument->format()
            : (string)$argument;
    }
}
