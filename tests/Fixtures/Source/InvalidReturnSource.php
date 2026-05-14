<?php

namespace HongXunPan\Validator\Tests\Fixtures\Source;

use HongXunPan\Validator\Definition\RuleDefinitionSourceInterface;

class InvalidReturnSource implements RuleDefinitionSourceInterface
{
    public static function resolve($name)
    {
        return 'invalid-definition';
    }
}
