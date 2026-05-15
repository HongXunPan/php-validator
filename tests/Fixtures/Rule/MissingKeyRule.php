<?php

namespace HongXunPan\Validator\Tests\Fixtures\Rule;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractValueRule;

class MissingKeyRule extends AbstractValueRule
{
    public static function validate($context)
    {
        return RuleResult::pass($context->value());
    }
}
