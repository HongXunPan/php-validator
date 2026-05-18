<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Support\StrictLiteralParser;

class PositiveNumericArgumentParser implements RuleArgumentParserInterface
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
     * @return PositiveNumericArgument
     */
    public function parse($rawArgument)
    {
        $value = $this->strictLiteralParser->parse($rawArgument);
        if (!is_int($value) && !is_float($value)) {
            throw new InvalidRuleArgumentException('正数参数必须是 JSON number literal');
        }

        return new PositiveNumericArgument($value);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        return $parsedArgument instanceof PositiveNumericArgument
            ? (string)$parsedArgument->value()
            : (string)$rawArgument;
    }
}
