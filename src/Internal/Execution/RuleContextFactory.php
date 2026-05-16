<?php

namespace HongXunPan\Validator\Internal\Execution;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Context\RuleValueReaderInterface;
use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Internal\Parsing\ParsedRuleToken;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Support\LiteralValueParser;

class RuleContextFactory
{
    /**
     * @var LiteralValueParser
     */
    private $literalValueParser;

    public function __construct(LiteralValueParser $literalValueParser)
    {
        $this->literalValueParser = $literalValueParser;
    }

    public function create(
        RuleTarget $ruleTarget,
        $paramName,
        ParsedRuleToken $parsedRule,
        TargetValueContext $targetValueContext,
        RuleValueReaderInterface $ruleValueReader
    ) {
        return new RuleContext(
            $ruleTarget->fieldPath(),
            $paramName,
            $parsedRule->rawArgument(),
            $targetValueContext->rawExists(),
            $targetValueContext->rawValue(),
            $targetValueContext->currentExists(),
            $targetValueContext->currentValue(),
            $ruleValueReader,
            $this->literalValueParser
        );
    }
}
