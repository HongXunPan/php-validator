<?php

namespace HongXunPan\Validator\Rule\Transform\Common;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueTransformRule;

class ToBoolRule extends AbstractPresentValueTransformRule
{
    const KEY = 'toBool';
    const MESSAGE = '$paramName must be boolean-like value';

    public static function validate(RuleContext $context)
    {
        $parsed = self::parseBooleanLike($context->value());
        if ($parsed === null) {
            return RuleResult::fail($context->value());
        }

        return RuleResult::pass($parsed);
    }

    /**
     * @param mixed $value
     *
     * @return bool|null
     */
    private static function parseBooleanLike($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            if ($value === 1) {
                return true;
            }

            if ($value === 0) {
                return false;
            }

            return null;
        }

        if (!is_string($value)) {
            return null;
        }

        $normalized = strtolower(trim($value));
        if (in_array($normalized, array('1', 'true', 'yes', 'on'), true)) {
            return true;
        }

        if (in_array($normalized, array('0', 'false', 'no', 'off'), true)) {
            return false;
        }

        return null;
    }
}
