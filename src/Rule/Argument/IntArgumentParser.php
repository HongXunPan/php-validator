<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Support\StrictLiteralParser;

class IntArgumentParser implements RuleArgumentParserInterface
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
     * @return IntArgument
     */
    public function parse($rawArgument)
    {
        $value = $this->strictLiteralParser->parse($rawArgument);
        if (!is_int($value)) {
            throw new InvalidRuleArgumentException('整数参数必须是 JSON integer literal');
        }

        return new IntArgument($value);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        return $parsedArgument instanceof IntArgument
            ? (string)$parsedArgument->value()
            : (string)$rawArgument;
    }
}
