<?php

namespace HongXunPan\Validator\Internal\State;

use HongXunPan\Validator\Context\ValidationOptions;
use HongXunPan\Validator\Internal\Execution\RuleExecutionOutcome;
use HongXunPan\Validator\Internal\Field\PathLabelMap;
use HongXunPan\Validator\Internal\Field\RuleTarget;
use HongXunPan\Validator\Internal\Field\TargetState;
use HongXunPan\Validator\Internal\Parsing\ParsedRuleToken;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Result\ValidationResult;
use HongXunPan\Validator\Support\PathAccessor;

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

    public function addUnknownField($paramName, $value)
    {
        $this->appendError(
            $paramName,
            $value,
            'unknown',
            '',
            'unknown field',
            RuleSet::unknownMessageTemplate()
        );
    }

    public function addFieldFailure(RuleTarget $ruleTarget, ParsedRuleToken $parsedRule, TargetState $targetState, RuleExecutionOutcome $outcome)
    {
        $paramName = $this->displayName($ruleTarget);

        if ($outcome->isUnsupported()) {
            $this->appendError(
                $paramName,
                $targetState->value(),
                'unsupported',
                $parsedRule->inputRuleKey(),
                'rule not support',
                RuleSet::unsupportedRuleMessageTemplate()
            );

            return;
        }

        $resolvedRule = $outcome->resolvedRule();

        $this->appendError(
            $paramName,
            $targetState->value(),
            $resolvedRule->finalRuleKey(),
            $parsedRule->rawArgument(),
            'result: false',
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

    private function appendError($paramName, $value, $rule, $ruleValue, $reason, $template, $displayRuleValue = null)
    {
        if ($displayRuleValue === null) {
            $displayRuleValue = $ruleValue;
        }

        $this->errors[] = $this->renderMessage($template, $paramName, $displayRuleValue);
        $this->detail[] = array(
            'param' => $paramName,
            'value' => $value,
            'rule' => $rule,
            'rule_value' => $ruleValue,
            'reason' => $reason,
        );
    }

    private function renderMessage($template, $paramName, $displayRuleValue)
    {
        $message = (string)$template;
        $message = str_replace('$paramName', (string)$paramName, $message);
        $message = str_replace('$rule', (string)$displayRuleValue, $message);

        return $message;
    }
}
