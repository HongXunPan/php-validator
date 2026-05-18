<?php

namespace HongXunPan\Validator\Rule\Assert\String;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Argument\FormatStringArgument;
use HongXunPan\Validator\Rule\Argument\FormatStringArgumentParser;
use HongXunPan\Validator\Rule\Marker\StringRule;

class RegexRule extends AbstractPresentValueAssertionRule implements StringRule
{
    const KEY = 'regex';
    const MESSAGE = '$paramName format is invalid';
    const ARGUMENT_PARSER = FormatStringArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        if (!is_string($value)) {
            return RuleResult::fail($value);
        }

        $pattern = self::pattern($context->parsedRuleArg());
        if ($pattern === '' || @preg_match($pattern, '') === false) {
            return RuleResult::fail($value);
        }

        return preg_match($pattern, $value) === 1
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }

    /**
     * @param mixed $argument
     *
     * @return string
     */
    private static function pattern($argument)
    {
        return $argument instanceof FormatStringArgument
            ? $argument->format()
            : (string)$argument;
    }
}
