<?php

namespace HongXunPan\Validator\Rule;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Context\PathLabelMap;

interface RuleInterface
{
    /**
     * @return string
     */
    public static function key();

    /**
     * @param mixed $arg
     *
     * @return string
     */
    public static function of($arg);

    /**
     * @return string
     */
    public static function defaultMessage();

    /**
     * @return string
     */
    public static function argumentParserClass();

    /**
     * @param mixed $rawArg
     *
     * @return string
     */
    public static function displayRuleValue($rawArg, PathLabelMap $pathLabelMap);

    /**
     * @return \HongXunPan\Validator\Result\RuleResult|array<string, mixed>|bool
     */
    public static function validate(RuleContext $context);
}
