<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class TimeLiteralArgumentParser implements RuleArgumentParserInterface
{
    /**
     * @param string $rawArgument
     *
     * @return TimeLiteralArgument
     */
    public function parse($rawArgument)
    {
        $literal = trim((string)$rawArgument);
        if (!$this->isAbsoluteTimeLiteral($literal)) {
            throw new InvalidRuleArgumentException('时间参数必须是明确的绝对时间字面量，例如 2026-05-14 10:00:00');
        }

        $timestamp = strtotime($literal);
        if ($timestamp === false) {
            throw new InvalidRuleArgumentException('时间参数不是合法时间');
        }

        return new TimeLiteralArgument($literal, $timestamp);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        return $parsedArgument instanceof TimeLiteralArgument
            ? $parsedArgument->literal()
            : (string)$rawArgument;
    }

    /**
     * @param string $literal
     *
     * @return bool
     */
    private function isAbsoluteTimeLiteral($literal)
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}([ T]\d{2}:\d{2}(:\d{2})?)?([+-]\d{2}:?\d{2}|Z)?$/', $literal) === 1;
    }
}
