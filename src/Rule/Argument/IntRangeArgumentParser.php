<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Support\StrictLiteralParser;

class IntRangeArgumentParser implements RuleArgumentParserInterface
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
     * @return IntRangeArgument
     */
    public function parse($rawArgument)
    {
        $range = $this->strictLiteralParser->parse($rawArgument);
        if (!is_array($range) || count($range) !== 2) {
            throw new InvalidRuleArgumentException('整数范围参数必须是 [min,max] 数组 literal');
        }

        $values = array_values($range);
        if (!is_int($values[0]) || !is_int($values[1])) {
            throw new InvalidRuleArgumentException('整数范围参数 min/max 必须是 JSON integer literal');
        }

        return new IntRangeArgument($values[0], $values[1]);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        return $parsedArgument instanceof IntRangeArgument
            ? '[' . $parsedArgument->min() . ',' . $parsedArgument->max() . ']'
            : (string)$rawArgument;
    }
}
