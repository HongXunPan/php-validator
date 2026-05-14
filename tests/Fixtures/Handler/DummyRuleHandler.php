<?php

namespace HongXunPan\Validator\Tests\Fixtures\Handler;

use HongXunPan\Validator\Handler\RuleHandlerInterface;

class DummyRuleHandler implements RuleHandlerInterface
{
    public static function validate($context)
    {
        return $context;
    }
}
