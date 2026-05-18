<?php

namespace HongXunPan\Validator\Rule\Assert\Common;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;

class DeclinedRule extends AbstractPresentValueAssertionRule
{
    const KEY = 'declined';
    const MESSAGE = '$paramName must be declined';

    public static function validate(RuleContext $context)
    {
        return self::isDeclined($context->value())
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private static function isDeclined($value)
    {
        if ($value === false || $value === 0) {
            return true;
        }

        if (!is_string($value)) {
            return false;
        }

        return in_array(strtolower(trim($value)), array('0', 'false', 'no', 'off'), true);
    }
}
