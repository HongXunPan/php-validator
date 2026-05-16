<?php

namespace HongXunPan\Validator;

use HongXunPan\Validator\Rule\CoreRules;

abstract class Validator
{
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

    public static function coreRules()
    {
        return CoreRules::map();
    }

    public static function extraRules()
    {
        return static::$extraRules;
    }

    public static function ruleAliases()
    {
        return static::$ruleAliases;
    }

    public static function ruleMessages()
    {
        return static::$ruleMessages;
    }

    protected static function kernel()
    {
        if (static::$kernel === null) {
            static::$kernel = ValidationKernel::create(static::class);
        }

        return static::$kernel;
    }

    public static function validate(array $data, array $rules, array $options = array())
    {
        return static::kernel()->validate($data, $rules, $options);
    }

    public static function validateAndNormalize(array $data, array $rules, array $options = array())
    {
        return static::kernel()->validateAndNormalize($data, $rules, $options);
    }

    public static function validateListAndNormalize(array $list, $rules, array $options = array())
    {
        return static::kernel()->validateListAndNormalize($list, $rules, $options);
    }

    public static function writeValidatedDataTo($result, $target)
    {
        return static::kernel()->writeValidatedDataTo($result, $target);
    }

    public static function validateAndWriteTo(array $data, array $rules, $target, array $options = array())
    {
        return static::kernel()->validateAndWriteTo($data, $rules, $target, $options);
    }
}
