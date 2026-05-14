<?php

namespace HongXunPan\Validator\Tests\Fixtures\Handler;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;

class KernelMinLengthValueHandler implements ValueRuleHandlerInterface
{
    public static function validate($context)
    {
        if (!($context instanceof RuleContext) || !is_string($context->value())) {
            return false;
        }

        return iconv_strlen($context->value()) >= (int)$context->ruleArgument();
    }
}
