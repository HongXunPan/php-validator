<?php

namespace HongXunPan\Validator;

use HongXunPan\Validator\Rule\CoreRules;

abstract class Validator
{
    /**
     * 当扩展配置较大、但不需要覆写方法合并时，可通过 provider class 常量拆文件。
     *
     * @var class-string|''
     */
    const EXTRA_RULES_PROVIDER_CLASS = '';
    /**
     * 当 alias 配置较大、但不需要覆写方法合并时，可通过 provider class 常量拆文件。
     *
     * @var class-string|''
     */
    const RULE_ALIASES_PROVIDER_CLASS = '';
    /**
     * 当 message 配置较大、但不需要覆写方法合并时，可通过 provider class 常量拆文件。
     *
     * @var class-string|''
     */
    const RULE_MESSAGES_PROVIDER_CLASS = '';

    /**
     * @var ValidationKernel|null
     */
    protected static $kernel;
    /**
     * @var array<string, string>
     */
    protected static $extraRules = array();
    /**
     * @var array<string, string>
     */
    protected static $ruleAliases = array();
    /**
     * @var array<string, string>
     */
    protected static $ruleMessages = array();

    /**
     * @return array<string, string>
     */
    public static function coreRules()
    {
        return CoreRules::map();
    }

    /**
     * @return array<string, string>
     */
    public static function extraRules()
    {
        if (static::EXTRA_RULES_PROVIDER_CLASS !== '') {
            return static::loadRuleConfigMap(static::EXTRA_RULES_PROVIDER_CLASS);
        }

        return static::defineExtraRules();
    }

    /**
     * @return array<string, string>
     */
    public static function ruleAliases()
    {
        if (static::RULE_ALIASES_PROVIDER_CLASS !== '') {
            return static::loadRuleConfigMap(static::RULE_ALIASES_PROVIDER_CLASS);
        }

        return static::defineRuleAliases();
    }

    /**
     * @return array<string, string>
     */
    public static function ruleMessages()
    {
        if (static::RULE_MESSAGES_PROVIDER_CLASS !== '') {
            return static::loadRuleConfigMap(static::RULE_MESSAGES_PROVIDER_CLASS);
        }

        return static::defineRuleMessages();
    }

    /**
     * @return array<string, string>
     */
    protected static function defineExtraRules()
    {
        return static::$extraRules;
    }

    /**
     * @return array<string, string>
     */
    protected static function defineRuleAliases()
    {
        return static::$ruleAliases;
    }

    /**
     * @return array<string, string>
     */
    protected static function defineRuleMessages()
    {
        return static::$ruleMessages;
    }

    /**
     * @return array<string, string>
     */
    protected static function loadRuleConfigMap($providerClass)
    {
        return (array)call_user_func(array((string)$providerClass, 'all'));
    }

    /**
     * @return ValidationKernel
     */
    protected static function kernel()
    {
        if (static::$kernel === null) {
            static::$kernel = ValidationKernel::create(static::class);
        }

        return static::$kernel;
    }

    /**
     * @return \HongXunPan\Validator\Result\ValidationResult
     */
    public static function validate(array $data, array $rules, array $options = array())
    {
        return static::kernel()->validate($data, $rules, $options);
    }

    /**
     * @return \HongXunPan\Validator\Result\ValidationResult
     */
    public static function validateAndNormalize(array $data, array $rules, array $options = array())
    {
        return static::kernel()->validateAndNormalize($data, $rules, $options);
    }

    /**
     * @param string|array<string, string> $rules
     *
     * @return \HongXunPan\Validator\Result\ValidationResult
     */
    public static function validateListAndNormalize(array $list, $rules, array $options = array())
    {
        return static::kernel()->validateListAndNormalize($list, $rules, $options);
    }

    /**
     * @param \HongXunPan\Validator\Result\ValidationResult $result
     * @param mixed $target
     *
     * @return mixed
     */
    public static function writeValidatedDataTo($result, $target)
    {
        return static::kernel()->writeValidatedDataTo($result, $target);
    }

    /**
     * @param mixed $target
     *
     * @return \HongXunPan\Validator\Result\ValidationResult
     */
    public static function validateAndWriteTo(array $data, array $rules, $target, array $options = array())
    {
        return static::kernel()->validateAndWriteTo($data, $rules, $target, $options);
    }
}
