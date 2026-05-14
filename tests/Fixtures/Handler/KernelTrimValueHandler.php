<?php

namespace HongXunPan\Validator\Tests\Fixtures\Handler;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;

class KernelTrimValueHandler implements ValueRuleHandlerInterface
{
    public static function validate($context)
    {
        if (!($context instanceof RuleContext) || !is_string($context->value())) {
            return false;
        }

        return array(
            'passed' => true,
            'value' => trim($context->value()),
        );
    }
}
