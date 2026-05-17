<?php

namespace HongXunPan\Validator\Tests\Fixtures\Rule;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueTransformRule;
use HongXunPan\Validator\Rule\Marker\TimeRule;

class FormatTimeRule extends AbstractPresentValueTransformRule implements TimeRule
{
    const KEY = 'formatTime';

    public static function validate(RuleContext $context)
    {
        return RuleResult::pass($context->value());
    }
}
