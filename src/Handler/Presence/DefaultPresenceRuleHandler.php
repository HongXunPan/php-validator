<?php

namespace HongXunPan\Validator\Handler\Presence;

use HongXunPan\Validator\Handler\PresenceRuleHandlerInterface;

class DefaultPresenceRuleHandler implements PresenceRuleHandlerInterface
{
    public static function validate($context)
    {
        return true;
    }
}
