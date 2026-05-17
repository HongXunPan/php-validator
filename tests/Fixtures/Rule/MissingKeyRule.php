<?php

namespace HongXunPan\Validator\Tests\Fixtures\Rule;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;

class MissingKeyRule extends AbstractPresentValueAssertionRule
{
    public static function validate(RuleContext $context)
    {
        return RuleResult::pass($context->value());
    }
}
