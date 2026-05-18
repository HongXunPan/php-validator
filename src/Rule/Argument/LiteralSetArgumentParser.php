<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Support\StrictLiteralParser;

class LiteralSetArgumentParser implements RuleArgumentParserInterface
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
     * @return LiteralSetArgument
     */
    public function parse($rawArgument)
    {
        $values = $this->strictLiteralParser->parse($rawArgument);
        if (!is_array($values)) {
            throw new InvalidRuleArgumentException('literal set 参数必须是数组 literal');
        }

        return new LiteralSetArgument($values);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        return $parsedArgument instanceof LiteralSetArgument
            ? json_encode($parsedArgument->values(), JSON_UNESCAPED_UNICODE)
            : (string)$rawArgument;
    }
}
