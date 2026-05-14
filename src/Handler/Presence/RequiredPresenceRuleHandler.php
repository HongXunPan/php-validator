<?php

namespace HongXunPan\Validator\Handler\Presence;

use HongXunPan\Validator\Handler\PresenceRuleHandlerInterface;

class RequiredPresenceRuleHandler implements PresenceRuleHandlerInterface
{
    public static function validate($context)
    {
        return $context->exists();
    }
}
