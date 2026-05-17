<?php

namespace HongXunPan\Validator\Internal\Execution;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Context\RuleValueReaderInterface;
use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Internal\Plan\CompiledRule;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Support\LiteralValueParser;

class RuleContextFactory
{
    /**
     * @var LiteralValueParser
     */
    private $literalValueParser;

    /**
     * @param LiteralValueParser $literalValueParser
     */
    public function __construct(LiteralValueParser $literalValueParser)
    {
        $this->literalValueParser = $literalValueParser;
    }

    /**
     * @param RuleTarget $ruleTarget
     * @param string $paramName
     * @param CompiledRule $compiledRule
     * @param TargetValueContext $targetValueContext
     * @param RuleValueReaderInterface $ruleValueReader
     *
     * @return RuleContext
     */
    public function create(
        RuleTarget $ruleTarget,
        $paramName,
        CompiledRule $compiledRule,
        TargetValueContext $targetValueContext,
        RuleValueReaderInterface $ruleValueReader
    ) {
        return new RuleContext(
            $ruleTarget->fieldPath(),
            $paramName,
            $compiledRule->parsedRule()->rawArgument(),
            $compiledRule->parsedArgument(),
            $targetValueContext->rawExists(),
            $targetValueContext->rawValue(),
            $targetValueContext->currentExists(),
            $targetValueContext->currentValue(),
            $ruleValueReader,
            $this->literalValueParser
        );
    }
}
