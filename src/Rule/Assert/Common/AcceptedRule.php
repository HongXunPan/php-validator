<?php

namespace HongXunPan\Validator\Rule\Assert\Common;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;

class AcceptedRule extends AbstractPresentValueAssertionRule
{
    const KEY = 'accepted';
    const MESSAGE = '$paramName must be accepted';

    public static function validate(RuleContext $context)
    {
        return self::isAccepted($context->value())
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private static function isAccepted($value)
    {
        if ($value === true || $value === 1) {
            return true;
        }

        if (!is_string($value)) {
            return false;
        }

        return in_array(strtolower(trim($value)), array('1', 'true', 'yes', 'on'), true);
    }
}
