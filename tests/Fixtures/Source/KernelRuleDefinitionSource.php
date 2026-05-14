<?php

namespace HongXunPan\Validator\Tests\Fixtures\Source;

use HongXunPan\Validator\Definition\RuleDefinition;
use HongXunPan\Validator\Definition\RuleDefinitionSourceInterface;
use HongXunPan\Validator\Definition\RuleName;
use HongXunPan\Validator\Message\MessageTemplate;
use HongXunPan\Validator\Tests\Fixtures\Handler\KernelMinLengthValueHandler;
use HongXunPan\Validator\Tests\Fixtures\Handler\KernelTrimValueHandler;

class KernelRuleDefinitionSource implements RuleDefinitionSourceInterface
{
    public static function resolve($name)
    {
        $name = RuleName::of($name)->value();

        if ($name === 'trimTest') {
            return RuleDefinition::value('trimTest', KernelTrimValueHandler::class)
                ->defaultMessage(MessageTemplate::of('$paramName trim failed'));
        }

        if ($name === 'minLengthTest') {
            return RuleDefinition::value('minLengthTest', KernelMinLengthValueHandler::class)
                ->defaultMessage(MessageTemplate::of('$paramName length must be at least $rule'));
        }

        return null;
    }
}
