<?php

namespace HongXunPan\Validator\Rule\Transform\Numeric;

use HongXunPan\Validator\Rule\AbstractPresentValueTransformRule;
use HongXunPan\Validator\Rule\Marker\NumericRule;

abstract class AbstractIntegerTransformRule extends AbstractPresentValueTransformRule implements NumericRule
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected static function isIntegerValue($value)
    {
        return $value !== null
            && $value !== ''
            && $value == intval($value);
    }

    /**
     * @param mixed $value
     *
     * @return int
     */
    protected static function toInteger($value)
    {
        return (int)$value;
    }
}
