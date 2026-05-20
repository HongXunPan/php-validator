<?php

namespace HongXunPan\Validator\Rule\Concern;

use HongXunPan\Validator\Rule\RuleArg;

trait BuildsFieldExpectedLiteralSetRule
{
    /**
     * @param string $fieldPath
     * @param array<int, mixed> $expectedValues
     *
     * @return string
     */
    final public static function ofFieldValues($fieldPath, array $expectedValues)
    {
        return static::of(RuleArg::fieldValues($fieldPath, $expectedValues));
    }
}
