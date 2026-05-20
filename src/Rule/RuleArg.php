<?php

namespace HongXunPan\Validator\Rule;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralArgument;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgument;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgument;
use HongXunPan\Validator\Rule\Argument\NumericRangeArgument;
use LogicException;

final class RuleArg
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param mixed $value
     */
    private function __construct($value)
    {
        $this->value = (string)$value;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public static function json($value)
    {
        return new self(static::encodeJson($value));
    }

    /**
     * @param string $fieldPath
     *
     * @return self
     */
    public static function field($fieldPath)
    {
        $argument = new FieldReferenceArgument($fieldPath);

        return new self($argument->fieldPath());
    }

    /**
     * @param string $fieldPath
     * @param mixed $expectedValue
     *
     * @return self
     */
    public static function fieldValue($fieldPath, $expectedValue)
    {
        $argument = new FieldExpectedLiteralArgument($fieldPath, $expectedValue);

        return new self($argument->fieldPath() . ',' . static::encodeJson($argument->expectedValue()));
    }

    /**
     * @param string $fieldPath
     * @param array<int, mixed> $expectedValues
     *
     * @return self
     */
    public static function fieldValues($fieldPath, array $expectedValues)
    {
        $argument = new FieldExpectedLiteralSetArgument($fieldPath, $expectedValues);

        return new self($argument->fieldPath() . ',' . static::encodeJson($argument->expectedValues()));
    }

    /**
     * @param int|float $min
     * @param int|float $max
     *
     * @return self
     */
    public static function range($min, $max)
    {
        if ((!is_int($min) && !is_float($min)) || (!is_int($max) && !is_float($max))) {
            throw new InvalidRuleArgumentException('范围参数只允许 int / float literal');
        }

        $argument = new NumericRangeArgument($min, $max);

        return new self(static::encodeJson(array($argument->min(), $argument->max())));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    private static function encodeJson($value)
    {
        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE);
        if ($encoded === false) {
            throw new LogicException('规则参数 JSON 编码失败：' . json_last_error_msg());
        }

        return $encoded;
    }
}
