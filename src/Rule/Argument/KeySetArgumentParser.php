<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Support\StrictLiteralParser;

class KeySetArgumentParser implements RuleArgumentParserInterface
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
     * @return KeySetArgument
     */
    public function parse($rawArgument)
    {
        $parsed = $this->strictLiteralParser->parse($rawArgument);
        if (is_string($parsed)) {
            return new KeySetArgument(array($parsed));
        }

        if (!is_array($parsed)) {
            throw new InvalidRuleArgumentException('key set 参数必须是 JSON string literal 或 string array literal');
        }

        return new KeySetArgument($parsed);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        if (!$parsedArgument instanceof KeySetArgument) {
            return (string)$rawArgument;
        }

        $keys = $parsedArgument->keys();
        if (count($keys) === 1) {
            return json_encode($keys[0], JSON_UNESCAPED_UNICODE);
        }

        return json_encode($keys, JSON_UNESCAPED_UNICODE);
    }
}
