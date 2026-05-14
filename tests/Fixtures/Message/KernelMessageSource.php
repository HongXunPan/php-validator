<?php

namespace HongXunPan\Validator\Tests\Fixtures\Message;

use HongXunPan\Validator\Message\MessageTemplate;
use HongXunPan\Validator\Message\RuleMessageSourceInterface;

class KernelMessageSource implements RuleMessageSourceInterface
{
    public static function resolve($name)
    {
        if ($name === 'trimTest') {
            return MessageTemplate::of('$paramName trim failed');
        }

        if ($name === 'minLengthTest') {
            return MessageTemplate::of('$paramName length must be at least $rule');
        }

        return null;
    }
}
