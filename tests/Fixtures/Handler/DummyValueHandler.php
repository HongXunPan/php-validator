<?php

namespace HongXunPan\Validator\Tests\Fixtures\Handler;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;

class DummyValueHandler implements ValueRuleHandlerInterface
{
    public static function validate($context)
    {
        return $context;
    }
}
