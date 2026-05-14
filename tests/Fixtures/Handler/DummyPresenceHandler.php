<?php

namespace HongXunPan\Validator\Tests\Fixtures\Handler;

use HongXunPan\Validator\Handler\PresenceRuleHandlerInterface;

class DummyPresenceHandler implements PresenceRuleHandlerInterface
{
    public static function validate($context)
    {
        return $context;
    }
}
