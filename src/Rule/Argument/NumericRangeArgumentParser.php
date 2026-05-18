<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Support\StrictLiteralParser;

class NumericRangeArgumentParser implements RuleArgumentParserInterface
{
    /**
     * @var StrictLiteralParser
     */
    private $strictLiteralParser;

    public function __construct()
    {
        $this->strictLiteralParser = new StrictLiteralParser();
    }

    /**
     * @param string $rawArgument
     *
     * @return NumericRangeArgument
     */
    public function parse($rawArgument)
    {
        $range = $this->strictLiteralParser->parse($rawArgument);
        if (!is_array($range) || count($range) !== 2) {
            throw new InvalidRuleArgumentException('数值范围参数必须是 [min,max] 数组 literal');
        }

        $values = array_values($range);
        if (!self::isNumber($values[0]) || !self::isNumber($values[1])) {
            throw new InvalidRuleArgumentException('数值范围参数 min/max 必须是 JSON number literal');
        }

        return new NumericRangeArgument($values[0], $values[1]);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        return $parsedArgument instanceof NumericRangeArgument
            ? '[' . $parsedArgument->min() . ',' . $parsedArgument->max() . ']'
            : (string)$rawArgument;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private static function isNumber($value)
    {
        return is_int($value) || is_float($value);
    }
}
