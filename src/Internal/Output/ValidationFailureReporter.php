<?php

namespace HongXunPan\Validator\Internal\Output;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Internal\Detail\ValidationDetailItem;
use HongXunPan\Validator\Internal\Execution\RuleExecutionOutcome;
use HongXunPan\Validator\Internal\Plan\CompiledRule;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Rules\RuleMessageTemplates;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Rule\Argument\NoArgumentParser;
use HongXunPan\Validator\Rule\Argument\RuleArgumentParserInterface;

class ValidationFailureReporter
{
    /**
     * @var ValidationOutput
     */
    private $output;
    /**
     * @var ValidationMessageRenderer
     */
    private $messageRenderer;
    /**
     * @var PathAccessor
     */
    private $pathAccessor;
    /**
     * @var PathLabelMap
     */
    private $pathLabelMap;
    /**
     * @var string
     */
    private $fieldPrefix;

    /**
     * @param ValidationOutput $output
     * @param ValidationMessageRenderer $messageRenderer
     * @param PathAccessor $pathAccessor
     * @param PathLabelMap $pathLabelMap
     * @param string $fieldPrefix
     */
    public function __construct(
        ValidationOutput $output,
        ValidationMessageRenderer $messageRenderer,
        PathAccessor $pathAccessor,
        PathLabelMap $pathLabelMap,
        $fieldPrefix
    ) {
        $this->output = $output;
        $this->messageRenderer = $messageRenderer;
        $this->pathAccessor = $pathAccessor;
        $this->pathLabelMap = $pathLabelMap;
        $this->fieldPrefix = (string)$fieldPrefix;
    }

    /**
     * @param RuleTarget $ruleTarget
     *
     * @return string
     */
    public function displayName(RuleTarget $ruleTarget)
    {
        return $this->pathAccessor->buildDisplayName(
            $ruleTarget->displayName(),
            $this->fieldPrefix
        );
    }

    /**
     * @param ValidationDetailItem $detailItem
     *
     * @return void
     */
    public function reportUnknownDetailItem(ValidationDetailItem $detailItem)
    {
        $this->output->appendFailure(
            $this->messageRenderer->render(
                RuleMessageTemplates::unknownTemplate(),
                $detailItem->param(),
                $detailItem->ruleValue()
            ),
            $detailItem
        );
    }

    /**
     * @param RuleTarget $ruleTarget
     * @param CompiledRule $compiledRule
     * @param TargetValueContext $targetValueContext
     * @param RuleExecutionOutcome $outcome
     *
     * @return void
     */
    public function reportTargetFailure(
        RuleTarget $ruleTarget,
        CompiledRule $compiledRule,
        TargetValueContext $targetValueContext,
        RuleExecutionOutcome $outcome
    ) {
        $paramName = $this->displayName($ruleTarget);
        $parsedRule = $compiledRule->parsedRule();

        if ($outcome->isUnsupported()) {
            $detailItem = ValidationDetailItem::unsupportedRule(
                $paramName,
                $targetValueContext->currentValue(),
                $parsedRule->inputRuleKey()
            );

            $this->output->appendFailure(
                $this->messageRenderer->render(
                    RuleMessageTemplates::unsupportedRuleTemplate(),
                    $detailItem->param(),
                    $detailItem->ruleValue()
                ),
                $detailItem
            );

            return;
        }

        $resolvedRule = $outcome->resolvedRule();
        $detailItem = ValidationDetailItem::ruleFailed(
            $paramName,
            $targetValueContext->currentValue(),
            $resolvedRule->finalRuleKey(),
            $parsedRule->rawArgument()
        );

        $displayRuleValue = $this->displayRuleValue($resolvedRule->ruleClass(), $compiledRule);

        $this->output->appendFailure(
            $this->messageRenderer->render(
                $outcome->messageTemplate(),
                $detailItem->param(),
                $displayRuleValue
            ),
            $detailItem
        );
    }

    /**
     * @param string $ruleClass
     *
     * @return string
     */
    private function displayRuleValue($ruleClass, CompiledRule $compiledRule)
    {
        $argumentParserClass = $compiledRule->argumentParserClass();
        if (
            is_string($argumentParserClass)
            && $argumentParserClass !== ''
            && $argumentParserClass !== NoArgumentParser::class
            && class_exists($argumentParserClass)
        ) {
            $argumentParser = new $argumentParserClass();
            if ($argumentParser instanceof RuleArgumentParserInterface) {
                return $argumentParser->display(
                    $compiledRule->parsedArgument(),
                    $compiledRule->parsedRule()->rawArgument(),
                    $this->pathLabelMap
                );
            }
        }

        return call_user_func(
            array($ruleClass, 'displayRuleValue'),
            $compiledRule->parsedRule()->rawArgument(),
            $this->pathLabelMap
        );
    }
}
