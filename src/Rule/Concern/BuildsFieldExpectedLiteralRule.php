<?php

namespace HongXunPan\Validator\Rule\Concern;

use HongXunPan\Validator\Rule\RuleArg;

trait BuildsFieldExpectedLiteralRule
{
    /**
     * @param string $fieldPath
     * @param mixed $expectedValue
     *
     * @return string
     */
    final public static function ofFieldValue($fieldPath, $expectedValue)
    {
        return static::of(RuleArg::fieldValue($fieldPath, $expectedValue));
    }
}
