<?php

namespace HongXunPan\Validator\Tests\Fixtures\Rule;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;
use HongXunPan\Validator\Rule\Marker\StringRule;

class MinLengthTestRule extends AbstractValueRule implements StringRule
{
    const KEY = 'minLengthTest';
    const MESSAGE = '$paramName length too short';

    public static function validate(RuleContext $context)
    {
        if (!is_string($context->value())) {
            return RuleResult::fail($context->value());
        }

        return strlen($context->value()) >= (int)$context->ruleArg()
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
