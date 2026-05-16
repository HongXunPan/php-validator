<?php

namespace HongXunPan\Validator\Internal\Output;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Internal\Detail\ValidationDetailItem;
use HongXunPan\Validator\Internal\Execution\RuleExecutionOutcome;
use HongXunPan\Validator\Internal\Parsing\ParsedRuleToken;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Rules\RuleMessageTemplates;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Internal\Context\TargetValueContext;

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

    public function displayName(RuleTarget $ruleTarget)
    {
        return $this->pathAccessor->buildDisplayName(
            $ruleTarget->displayName(),
            $this->fieldPrefix
        );
    }

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

    public function reportTargetFailure(
        RuleTarget $ruleTarget,
        ParsedRuleToken $parsedRule,
        TargetValueContext $targetValueContext,
        RuleExecutionOutcome $outcome
    ) {
        $paramName = $this->displayName($ruleTarget);

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

        $displayRuleValue = call_user_func(
            array($resolvedRule->ruleClass(), 'displayRuleValue'),
            $parsedRule->rawArgument(),
            $this->pathLabelMap
        );

        $this->output->appendFailure(
            $this->messageRenderer->render(
                $outcome->messageTemplate(),
                $detailItem->param(),
                $displayRuleValue
            ),
            $detailItem
        );
    }
}
