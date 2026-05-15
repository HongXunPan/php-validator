<?php

namespace HongXunPan\Validator\Internal\State;

use HongXunPan\Validator\Context\ValidationOptions;
use HongXunPan\Validator\Internal\Detail\ValidationDetailItem;
use HongXunPan\Validator\Internal\Execution\RuleExecutionOutcome;
use HongXunPan\Validator\Internal\Path\PathLabelMap;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Internal\Target\TargetValueContext;
use HongXunPan\Validator\Internal\Target\TargetValueContextStore;
use HongXunPan\Validator\Internal\Parsing\ParsedRuleToken;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Result\ValidationResult;
use HongXunPan\Validator\Internal\Path\PathAccessor;

class ValidationState
{
    /**
     * @var array
     */
    private $rawData;
    /**
     * @var ValidationOptions
     */
    private $options;
    /**
     * @var bool
     */
    private $normalizeOutput;
    /**
     * @var PathAccessor
     */
    private $pathAccessor;
    /**
     * @var PathLabelMap
     */
    private $pathLabelMap;
    /**
     * @var array
     */
    private $errors = array();
    /**
     * @var array
     */
    private $detail = array();
    /**
     * @var array
     */
    private $validatedData = array();
    /**
     * @var TargetValueContextStore
     */
    private $targetValueContextStore;

    public function __construct(
        array $rawData,
        ValidationOptions $options,
        $normalizeOutput,
        PathAccessor $pathAccessor,
        PathLabelMap $pathLabelMap
    )
    {
        $this->rawData = $rawData;
        $this->options = $options;
        $this->normalizeOutput = (bool)$normalizeOutput;
        $this->pathAccessor = $pathAccessor;
        $this->pathLabelMap = $pathLabelMap;
        $this->targetValueContextStore = new TargetValueContextStore();
    }

    public function rawData()
    {
        return $this->rawData;
    }

    public function options()
    {
        return $this->options;
    }

    public function strict()
    {
        return $this->options->strict();
    }

    public function fieldPrefix()
    {
        return $this->options->fieldPrefix();
    }

    public function normalizeOutput()
    {
        return $this->normalizeOutput;
    }

    public function pathAccessor()
    {
        return $this->pathAccessor;
    }

    public function writeValidatedField($fieldPath, $value)
    {
        $this->pathAccessor->setValue($this->validatedData, $fieldPath, $value);
    }

    public function rememberTargetValueContext($targetPath, TargetValueContext $targetValueContext)
    {
        $this->targetValueContextStore->remember($targetPath, $targetValueContext);
    }

    public function targetValueContextStore()
    {
        return $this->targetValueContextStore;
    }

    public function materializedTargetValue($targetPath)
    {
        return $this->targetValueContextStore->materializedPathValue($targetPath);
    }

    public function addUnknownField($paramName, $value)
    {
        $this->appendDetailItem(
            ValidationDetailItem::unknownField($paramName, $value),
            RuleSet::unknownMessageTemplate()
        );
    }

    public function addTargetFailure(RuleTarget $ruleTarget, ParsedRuleToken $parsedRule, TargetValueContext $targetValueContext, RuleExecutionOutcome $outcome)
    {
        $paramName = $this->displayName($ruleTarget);

        if ($outcome->isUnsupported()) {
            $this->appendDetailItem(
                ValidationDetailItem::unsupportedRule(
                    $paramName,
                    $targetValueContext->currentValue(),
                    $parsedRule->inputRuleKey()
                ),
                RuleSet::unsupportedRuleMessageTemplate()
            );

            return;
        }

        $resolvedRule = $outcome->resolvedRule();

        $this->appendDetailItem(
            ValidationDetailItem::ruleFailed(
                $paramName,
                $targetValueContext->currentValue(),
                $resolvedRule->finalRuleKey(),
                $parsedRule->rawArgument()
            ),
            $outcome->messageTemplate(),
            call_user_func(
                array($resolvedRule->ruleClass(), 'displayRuleValue'),
                $parsedRule->rawArgument(),
                $this->pathLabelMap
            )
        );
    }

    public function displayName(RuleTarget $ruleTarget)
    {
        return $this->pathAccessor->buildDisplayName(
            $ruleTarget->displayName(),
            $this->fieldPrefix()
        );
    }

    public function toValidationResult()
    {
        if (empty($this->errors)) {
            return ValidationResult::success($this->validatedData);
        }

        return ValidationResult::failure($this->errors, $this->detail, $this->validatedData);
    }

    private function appendDetailItem(ValidationDetailItem $detailItem, $template, $displayRuleValue = null)
    {
        if ($displayRuleValue === null) {
            $displayRuleValue = $detailItem->ruleValue();
        }

        $this->errors[] = $this->renderMessage($template, $detailItem->param(), $displayRuleValue);
        $this->detail[] = $detailItem->toArray();
    }

    private function renderMessage($template, $paramName, $displayRuleValue)
    {
        $message = (string)$template;
        $message = str_replace('$paramName', (string)$paramName, $message);
        $message = str_replace('$rule', (string)$displayRuleValue, $message);

        return $message;
    }
}
