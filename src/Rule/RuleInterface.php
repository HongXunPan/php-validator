<?php

namespace HongXunPan\Validator\Rule;

use HongXunPan\Validator\Context\PathLabelMap;

interface RuleInterface
{
    public static function key();

    public static function of($arg);

    public static function defaultMessage();

    public static function displayRuleValue($rawArg, PathLabelMap $pathLabelMap);

    public static function validate($context);
}
