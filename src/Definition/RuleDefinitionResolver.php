<?php

namespace HongXunPan\Validator\Definition;

use HongXunPan\Validator\Exception\InvalidRuleDefinitionException;
use HongXunPan\Validator\Exception\RuleNameReservedException;

/**
 * RuleDefinitionResolver 承接规则定义解析与缓存。
 */
class RuleDefinitionResolver
{
    private $coreSourceClass;
    private $extraSourceClasses;
    private $cache;

    public function __construct($coreSourceClass, array $extraSourceClasses)
    {
        $this->coreSourceClass = $coreSourceClass;
        $this->extraSourceClasses = array_values($extraSourceClasses);
        $this->cache = array();
    }

    public static function create(array $extraSourceClasses, $coreSourceClass = 'HongXunPan\Validator\Definition\CoreRuleDefinitionSource')
    {
        return new static($coreSourceClass, $extraSourceClasses);
    }

    public function resolve($name)
    {
        $ruleName = RuleName::of($name);
        $cacheKey = $ruleName->value();

        if (array_key_exists($cacheKey, $this->cache)) {
            return $this->cache[$cacheKey];
        }

        $coreDefinition = $this->resolveFromSourceClass($this->coreSourceClass, $ruleName);
        if ($coreDefinition !== null) {
            $this->assertNoExtraSourceOverridesCore($ruleName);
            $this->cache[$cacheKey] = $coreDefinition;

            return $coreDefinition;
        }

        foreach ($this->extraSourceClasses as $sourceClass) {
            $definition = $this->resolveFromSourceClass($sourceClass, $ruleName);
            if ($definition !== null) {
                $this->cache[$cacheKey] = $definition;

                return $definition;
            }
        }

        $this->cache[$cacheKey] = null;

        return null;
    }

    private function assertNoExtraSourceOverridesCore(RuleName $ruleName)
    {
        foreach ($this->extraSourceClasses as $sourceClass) {
            $definition = $this->resolveFromSourceClass($sourceClass, $ruleName);
            if ($definition !== null) {
                throw new RuleNameReservedException('extra source 不允许覆盖 core rule：' . $ruleName->value());
            }
        }
    }

    private function resolveFromSourceClass($sourceClass, RuleName $ruleName)
    {
        if (!is_string($sourceClass) || $sourceClass === '') {
            throw new InvalidRuleDefinitionException('RuleDefinitionSource 非法');
        }

        if (!is_subclass_of($sourceClass, RuleDefinitionSourceInterface::class)) {
            throw new InvalidRuleDefinitionException('RuleDefinitionSource 未实现接口：' . $sourceClass);
        }

        $definition = $sourceClass::resolve($ruleName);
        if ($definition === null) {
            return null;
        }

        if (!($definition instanceof RuleDefinition)) {
            throw new InvalidRuleDefinitionException('RuleDefinitionSource 返回值非法：' . $sourceClass);
        }

        if (!$definition->name()->equals($ruleName)) {
            throw new InvalidRuleDefinitionException('RuleDefinitionSource 返回了不匹配的规则名：' . $sourceClass);
        }

        $definition->assertValid();

        return $definition;
    }
}
