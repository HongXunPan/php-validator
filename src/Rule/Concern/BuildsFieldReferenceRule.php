<?php

namespace HongXunPan\Validator\Rule\Concern;

use HongXunPan\Validator\Rule\RuleArg;

trait BuildsFieldReferenceRule
{
    /**
     * @param string $fieldPath
     *
     * @return string
     */
    final public static function ofField($fieldPath)
    {
        return static::of(RuleArg::field($fieldPath));
    }
}
