<?php

namespace HongXunPan\Validator\Tests\Fixtures\Source;

use HongXunPan\Validator\Definition\RuleDefinition;
use HongXunPan\Validator\Definition\RuleDefinitionSourceInterface;
use HongXunPan\Validator\Definition\RuleName;
use HongXunPan\Validator\Message\MessageTemplate;
use HongXunPan\Validator\Tests\Fixtures\Handler\DummyPresenceHandler;

class CountingCoreSource implements RuleDefinitionSourceInterface
{
    private static $calls = array();

    public static function reset()
    {
        self::$calls = array();
    }

    public static function callsFor($name)
    {
        return isset(self::$calls[$name]) ? self::$calls[$name] : 0;
    }

    public static function resolve($name)
    {
        $name = RuleName::of($name)->value();
        self::$calls[$name] = self::callsFor($name) + 1;

        if ($name !== 'required') {
            return null;
        }

        return RuleDefinition::presence('required', DummyPresenceHandler::class)
            ->defaultMessage(MessageTemplate::of('required'));
    }
}
