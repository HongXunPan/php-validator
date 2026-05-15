<?php

namespace HongXunPan\Validator\Internal\Rules;

use HongXunPan\Validator\Exception\InvalidRuleClassException;
use HongXunPan\Validator\Exception\RuleNameReservedException;
use HongXunPan\Validator\Exception\ValidatorException;
use HongXunPan\Validator\Rule\PresenceRuleInterface;
use HongXunPan\Validator\Rule\RuleInterface;
use HongXunPan\Validator\Rule\ValueRuleInterface;

class RuleSet
{
    const DEFAULT_MESSAGE_TEMPLATE = '$paramName validate failed';
    const UNKNOWN_MESSAGE_TEMPLATE = '$paramName is unknown';
    const UNSUPPORTED_RULE_MESSAGE_TEMPLATE = '$paramName rule is unsupported: $rule';

    private $ruleMap = array();
    private $aliasMap = array();
    private $messageMap = array();

    public static function fromValidatorClass($validatorClass)
    {
        $ruleSet = new static();
        $ruleSet->registerCoreRules(call_user_func(array($validatorClass, 'coreRules')));
        $ruleSet->registerExtraRules(call_user_func(array($validatorClass, 'extraRules')));
        $ruleSet->registerAliases(call_user_func(array($validatorClass, 'ruleAliases')));
        $ruleSet->registerMessages(call_user_func(array($validatorClass, 'ruleMessages')));

        return $ruleSet;
    }

    public static function unknownMessageTemplate()
    {
        return self::UNKNOWN_MESSAGE_TEMPLATE;
    }

    public static function unsupportedRuleMessageTemplate()
    {
        return self::UNSUPPORTED_RULE_MESSAGE_TEMPLATE;
    }

    public function resolveRule($inputRuleKey)
    {
        $inputRuleKey = (string)$inputRuleKey;

        if (array_key_exists($inputRuleKey, $this->ruleMap)) {
            return new ResolvedRule($inputRuleKey, $inputRuleKey, $this->ruleMap[$inputRuleKey]);
        }

        if (!array_key_exists($inputRuleKey, $this->aliasMap)) {
            return null;
        }

        $finalRuleKey = $this->aliasMap[$inputRuleKey];

        return new ResolvedRule($inputRuleKey, $finalRuleKey, $this->ruleMap[$finalRuleKey]);
    }

    public function resolveMessage(ResolvedRule $resolvedRule)
    {
        $finalRuleKey = $resolvedRule->finalRuleKey();
        $ruleClass = $resolvedRule->ruleClass();

        if (array_key_exists($finalRuleKey, $this->messageMap)) {
            return $this->messageMap[$finalRuleKey];
        }

        $message = call_user_func(array($ruleClass, 'defaultMessage'));

        return $message === null || $message === ''
            ? self::DEFAULT_MESSAGE_TEMPLATE
            : $message;
    }

    private function registerCoreRules(array $ruleMap)
    {
        foreach ($ruleMap as $ruleKey => $ruleClass) {
            $this->assertRuleClass($ruleKey, $ruleClass);
            $this->ruleMap[$ruleKey] = $ruleClass;
        }
    }

    private function registerExtraRules(array $ruleMap)
    {
        foreach ($ruleMap as $ruleKey => $ruleClass) {
            if (array_key_exists($ruleKey, $this->ruleMap)) {
                throw new RuleNameReservedException('规则名不允许覆盖：' . $ruleKey);
            }

            $this->assertRuleClass($ruleKey, $ruleClass);
            $this->ruleMap[$ruleKey] = $ruleClass;
        }
    }

    private function registerAliases(array $aliases)
    {
        foreach ($aliases as $aliasKey => $finalRuleKey) {
            $aliasKey = (string)$aliasKey;
            $finalRuleKey = (string)$finalRuleKey;

            if (array_key_exists($aliasKey, $this->ruleMap)) {
                throw new RuleNameReservedException('alias key 与真实规则名冲突：' . $aliasKey);
            }

            if (array_key_exists($aliasKey, $this->aliasMap)) {
                throw new ValidatorException('alias 重复：' . $aliasKey);
            }

            if (!array_key_exists($finalRuleKey, $this->ruleMap)) {
                throw new ValidatorException('alias 指向不存在的规则：' . $finalRuleKey);
            }

            $this->aliasMap[$aliasKey] = $finalRuleKey;
        }
    }

    private function registerMessages(array $messages)
    {
        foreach ($messages as $ruleKey => $template) {
            $this->messageMap[(string)$ruleKey] = (string)$template;
        }
    }

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
