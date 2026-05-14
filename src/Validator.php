<?php

namespace HongXunPan\Validator;

/**
 * Validator 对外静态入口。
 */
abstract class Validator
{
    protected static $kernel;
    protected static $extraDefinitionSources = array();
    protected static $messageSources = array();

    protected static function kernel()
    {
        if (static::$kernel === null) {
            static::$kernel = ValidationKernel::create(
                static::$extraDefinitionSources,
                static::$messageSources
            );
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
