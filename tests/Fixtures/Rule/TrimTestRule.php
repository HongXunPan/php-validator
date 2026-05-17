<?php

namespace HongXunPan\Validator\Tests\Fixtures\Rule;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueNormalizationRule;
use HongXunPan\Validator\Rule\Marker\StringRule;

class TrimTestRule extends AbstractPresentValueNormalizationRule implements StringRule
{
    const KEY = 'trimTest';
    const MESSAGE = '$paramName must be string';

    public static function validate(RuleContext $context)
    {
        if (!is_string($context->value())) {
            return RuleResult::fail($context->value());
        }

        return RuleResult::pass(trim($context->value()));
    }
}
