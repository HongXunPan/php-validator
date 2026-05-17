<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Support\StrictLiteralParser;

class FieldExpectedLiteralSetArgumentParser implements RuleArgumentParserInterface
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
     * @return FieldExpectedLiteralSetArgument
     */
    public function parse($rawArgument)
    {
        list($fieldPath, $rawExpectedValues) = $this->splitFieldAndLiteral($rawArgument);
        $expectedValues = $this->strictLiteralParser->parse($rawExpectedValues);

        if (!is_array($expectedValues)) {
            throw new InvalidRuleArgumentException('字段集合参数的 expected values 必须是数组 literal');
        }

        return new FieldExpectedLiteralSetArgument($fieldPath, $expectedValues);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        if (!$parsedArgument instanceof FieldExpectedLiteralSetArgument) {
            return (string)$rawArgument;
        }

        return $pathLabelMap->resolve($parsedArgument->fieldPath(), $parsedArgument->fieldPath())
            . ','
            . json_encode($parsedArgument->expectedValues(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $rawArgument
     *
     * @return array{0: string, 1: string}
     */
    private function splitFieldAndLiteral($rawArgument)
    {
        $parts = explode(',', (string)$rawArgument, 2);
        if (count($parts) < 2) {
            throw new InvalidRuleArgumentException('字段集合参数必须形如 field,[literal,...]');
        }

        return array($parts[0], $parts[1]);
    }
}
