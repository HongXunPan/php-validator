<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Support\StrictLiteralParser;

class FieldExpectedLiteralArgumentParser implements RuleArgumentParserInterface
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
     * @return FieldExpectedLiteralArgument
     */
    public function parse($rawArgument)
    {
        list($fieldPath, $rawExpectedValue) = $this->splitFieldAndLiteral($rawArgument, '字段等值参数必须形如 field,literal');

        return new FieldExpectedLiteralArgument(
            $fieldPath,
            $this->strictLiteralParser->parse($rawExpectedValue)
        );
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        if (!$parsedArgument instanceof FieldExpectedLiteralArgument) {
            return (string)$rawArgument;
        }

        return $pathLabelMap->resolve($parsedArgument->fieldPath(), $parsedArgument->fieldPath())
            . ','
            . json_encode($parsedArgument->expectedValue(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $rawArgument
     * @param string $errorMessage
     *
     * @return array{0: string, 1: string}
     */
    private function splitFieldAndLiteral($rawArgument, $errorMessage)
    {
        $parts = explode(',', (string)$rawArgument, 2);
        if (count($parts) < 2) {
            throw new InvalidRuleArgumentException($errorMessage);
        }

        return array($parts[0], $parts[1]);
    }
}
