<?php

namespace HongXunPan\Validator\Tests\Fixtures\Rule;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Tests\Fixtures\Rule\Argument\CommaPairArgumentParser;

class ParsedPairRule extends AbstractPresentValueAssertionRule
{
    const KEY = 'parsedPair';
    const ARGUMENT_PARSER = CommaPairArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        return is_array($context->parsedRuleArg())
            ? RuleResult::pass($context->value())
            : RuleResult::fail($context->value());
    }
}
