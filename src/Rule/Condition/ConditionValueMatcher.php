<?php

namespace HongXunPan\Validator\Rule\Condition;

class ConditionValueMatcher
{
    /**
     * @param mixed $actualValue
     * @param mixed $expectedValue
     *
     * @return bool
     */
    public static function eq($actualValue, $expectedValue)
    {
        return $actualValue === $expectedValue;
    }

    /**
     * @param mixed $actualValue
     * @param mixed $expectedValue
     *
     * @return bool
     */
    public static function notEq($actualValue, $expectedValue)
    {
        return $actualValue !== $expectedValue;
    }

    /**
     * @param mixed $actualValue
     * @param array<int, mixed> $expectedValues
     *
     * @return bool
     */
    public static function in($actualValue, array $expectedValues)
    {
        return in_array($actualValue, $expectedValues, true);
    }

    /**
     * @param mixed $actualValue
     * @param array<int, mixed> $expectedValues
     *
     * @return bool
     */
    public static function notIn($actualValue, array $expectedValues)
    {
        return !in_array($actualValue, $expectedValues, true);
    }
}
