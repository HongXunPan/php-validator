<?php

namespace HongXunPan\Validator\Internal\Rules;

use HongXunPan\Validator\Exception\InvalidRuleClassException;
use HongXunPan\Validator\Exception\RuleNameReservedException;
use HongXunPan\Validator\Rule\PresenceRuleInterface;
use HongXunPan\Validator\Rule\RuleInterface;
use HongXunPan\Validator\Rule\ValueRuleInterface;

class RuleRegistry
{
    /**
     * @var array
     */
    private $ruleMap = array();

    /**
     * @param array<string, string> $ruleMap
     *
     * @return void
     */
    public function registerCoreRules(array $ruleMap)
    {
        foreach ($ruleMap as $ruleKey => $ruleClass) {
            $this->assertRuleClass($ruleKey, $ruleClass);
            $this->ruleMap[(string)$ruleKey] = $ruleClass;
        }
    }

    /**
     * @param array<string, string> $ruleMap
     *
     * @return void
     */
    public function registerExtraRules(array $ruleMap)
    {
        foreach ($ruleMap as $ruleKey => $ruleClass) {
            $ruleKey = (string)$ruleKey;
            if ($this->hasRule($ruleKey)) {
                throw new RuleNameReservedException('规则名不允许覆盖：' . $ruleKey);
            }

            $this->assertRuleClass($ruleKey, $ruleClass);
            $this->ruleMap[$ruleKey] = $ruleClass;
        }
    }

    /**
     * @param string $ruleKey
     *
     * @return bool
     */
    public function hasRule($ruleKey)
    {
        return array_key_exists((string)$ruleKey, $this->ruleMap);
    }

    /**
     * @param string $ruleKey
     *
     * @return string|null
     */
    public function ruleClass($ruleKey)
    {
        $ruleKey = (string)$ruleKey;
        if (!$this->hasRule($ruleKey)) {
            return null;
        }

        return $this->ruleMap[$ruleKey];
    }

    /**
     * @param string $declaredRuleKey
     * @param string $ruleClass
     *
     * @return void
     */
    private function assertRuleClass($declaredRuleKey, $ruleClass)
    {
        if (!is_string($ruleClass) || $ruleClass === '') {
            throw new InvalidRuleClassException('规则类非法：' . $declaredRuleKey);
        }

        if (!class_exists($ruleClass)) {
            throw new InvalidRuleClassException('规则类不存在：' . $ruleClass);
        }

        if (!is_subclass_of($ruleClass, RuleInterface::class)) {
            throw new InvalidRuleClassException('规则类未实现 RuleInterface：' . $ruleClass);
        }

        if (call_user_func(array($ruleClass, 'key')) !== (string)$declaredRuleKey) {
            throw new InvalidRuleClassException('规则 KEY 与声明 key 不一致：' . $declaredRuleKey);
        }

        $isPresence = is_subclass_of($ruleClass, PresenceRuleInterface::class);
        $isValue = is_subclass_of($ruleClass, ValueRuleInterface::class);
        if ($isPresence === $isValue) {
            throw new InvalidRuleClassException('规则必须且只能属于一个阶段：' . $ruleClass);
        }
    }
}
