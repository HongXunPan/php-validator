<?php

namespace HongXunPan\Validator\Rule\Assert\String;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Marker\StringRule;

abstract class AbstractStringContentRule extends AbstractPresentValueAssertionRule implements StringRule
{
    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        if (!is_string($value)) {
            return RuleResult::fail($value);
        }

        return static::matches($value)
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    abstract protected static function matches($value);

    /**
     * @param string $value
     *
     * @return bool
     */
    protected static function isAscii($value)
    {
        return preg_match('/^[\x00-\x7F]*$/', $value) === 1;
    }
}
