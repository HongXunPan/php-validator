<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Support\StrictLiteralParser;

class NonNegativeIntArgumentParser implements RuleArgumentParserInterface
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
     * @return NonNegativeIntArgument
     */
    public function parse($rawArgument)
    {
        $value = $this->strictLiteralParser->parse($rawArgument);
        if (!is_int($value)) {
            throw new InvalidRuleArgumentException('非负整数参数必须是 JSON integer literal');
        }

        return new NonNegativeIntArgument($value);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        return $parsedArgument instanceof NonNegativeIntArgument
            ? (string)$parsedArgument->value()
            : (string)$rawArgument;
    }
}
