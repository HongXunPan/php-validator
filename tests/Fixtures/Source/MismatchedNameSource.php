<?php

namespace HongXunPan\Validator\Tests\Fixtures\Source;

use HongXunPan\Validator\Definition\RuleDefinition;
use HongXunPan\Validator\Definition\RuleDefinitionSourceInterface;
use HongXunPan\Validator\Message\MessageTemplate;
use HongXunPan\Validator\Tests\Fixtures\Handler\DummyValueHandler;

class MismatchedNameSource implements RuleDefinitionSourceInterface
{
    public static function resolve($name)
    {
        return RuleDefinition::value('anotherName', DummyValueHandler::class)
            ->defaultMessage(MessageTemplate::of('anotherName'));
    }
}
