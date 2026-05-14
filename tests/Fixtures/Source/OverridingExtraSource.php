<?php

namespace HongXunPan\Validator\Tests\Fixtures\Source;

use HongXunPan\Validator\Definition\RuleDefinition;
use HongXunPan\Validator\Definition\RuleDefinitionSourceInterface;
use HongXunPan\Validator\Definition\RuleName;
use HongXunPan\Validator\Message\MessageTemplate;
use HongXunPan\Validator\Tests\Fixtures\Handler\DummyPresenceHandler;

class OverridingExtraSource implements RuleDefinitionSourceInterface
{
    public static function resolve($name)
    {
        $name = RuleName::of($name)->value();

        if ($name !== 'required') {
            return null;
        }

        return RuleDefinition::presence('required', DummyPresenceHandler::class)
            ->defaultMessage(MessageTemplate::of('required'));
    }
}
