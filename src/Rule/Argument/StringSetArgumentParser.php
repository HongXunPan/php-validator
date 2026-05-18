<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Support\StrictLiteralParser;

class StringSetArgumentParser implements RuleArgumentParserInterface
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
     * @return StringSetArgument
     */
    public function parse($rawArgument)
    {
        $parsed = $this->strictLiteralParser->parse($rawArgument);
        if (is_string($parsed)) {
            return new StringSetArgument(array($parsed));
        }

        if (!is_array($parsed)) {
            throw new InvalidRuleArgumentException('字符串参数必须是 JSON string literal 或 string array literal');
        }

        return new StringSetArgument($parsed);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        if (!$parsedArgument instanceof StringSetArgument) {
            return (string)$rawArgument;
        }

        $values = $parsedArgument->values();
        if (count($values) === 1) {
            return json_encode($values[0], JSON_UNESCAPED_UNICODE);
        }

        return json_encode($values, JSON_UNESCAPED_UNICODE);
    }
}
