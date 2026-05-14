<?php

namespace HongXunPan\Validator\Definition;

interface RuleDefinitionSourceInterface
{
    /**
     * @param RuleName $name
     * @return RuleDefinition|null
     */
    public static function resolve($name);
}
