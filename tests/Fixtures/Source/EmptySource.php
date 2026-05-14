<?php

namespace HongXunPan\Validator\Tests\Fixtures\Source;

use HongXunPan\Validator\Definition\RuleDefinitionSourceInterface;

class EmptySource implements RuleDefinitionSourceInterface
{
    public static function resolve($name)
    {
        return null;
    }
}
