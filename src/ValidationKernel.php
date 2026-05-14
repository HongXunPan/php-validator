<?php

namespace HongXunPan\Validator;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Context\ValidationOptions;
use HongXunPan\Validator\Definition\RuleDefinitionResolver;
use HongXunPan\Validator\Exception\InvalidValidatedDataTargetException;
use HongXunPan\Validator\Message\MessageTemplate;
use HongXunPan\Validator\Message\RuleMessageSourceInterface;
use HongXunPan\Validator\Output\ArrayAccessValidatedDataWriter;
use HongXunPan\Validator\Result\ValidationResult;
use HongXunPan\Validator\Support\LiteralValueParser;
use HongXunPan\Validator\Support\PathAccessor;
use HongXunPan\Validator\Support\RuleParser;
use HongXunPan\Validator\Support\RuleResultNormalizer;
use HongXunPan\Validator\Support\UnknownFieldCollector;
use LogicException;

/**
 * ValidationKernel 承接校验执行编排。
 */
class ValidationKernel
{
    private $definitionResolver;
    private $messageSources;
    private $ruleParser;
    private $pathAccessor;
    private $literalValueParser;
    private $ruleResultNormalizer;
    private $unknownFieldCollector;
    private $validatedDataWriter;

    public function __construct(RuleDefinitionResolver $definitionResolver, array $messageSources)
    {
        $this->definitionResolver = $definitionResolver;
        $this->messageSources = array_values($messageSources);
        $this->ruleParser = new RuleParser();
        $this->pathAccessor = new PathAccessor();
        $this->literalValueParser = new LiteralValueParser();
        $this->ruleResultNormalizer = new RuleResultNormalizer();
        $this->unknownFieldCollector = new UnknownFieldCollector($this->ruleParser, $this->pathAccessor);
        $this->validatedDataWriter = new ArrayAccessValidatedDataWriter();
    }

    public static function create(array $extraDefinitionSources, array $messageSources)
    {
        return new static(
            RuleDefinitionResolver::create($extraDefinitionSources),
            $messageSources
        );
    }

    public function definitionResolver()
    {
        return $this->definitionResolver;
    }

    public function messageSources()
    {
        return $this->messageSources;
    }

    public function validate(array $data, array $rules, array $options = array())
    {
        return $this->doValidate($data, $rules, $this->normalizeOptions($options), false);
    }

    public function validateAndNormalize(array $data, array $rules, array $options = array())
    {
        return $this->doValidate($data, $rules, $this->normalizeOptions($options), true);
    }

    public function validateListAndNormalize(array $list, $rules, array $options = array())
    {
        $validationOptions = $this->normalizeOptions($options);
        $errorCount = 0;
        $errors = array();
        $detail = array();
        $validatedData = array();
        $position = 0;

        foreach ($list as $item) {
            $position++;
            $itemPrefix = $this->pathAccessor->join(
                $validationOptions->fieldPrefix(),
                (string)$position
            );

            if (is_array($rules)) {
                if (!is_array($item)) {
                    $this->appendError(
                        $errors,
                        $detail,
                        $errorCount,
                        $itemPrefix,
                        $item,
                        'array',
                        '',
                        'list item not array'
                    );
                    continue;
                }

                $itemResult = $this->doValidate(
                    $item,
                    $rules,
                    ValidationOptions::fromArray(array_merge($validationOptions->toArray(), array(
                        'field_prefix' => $itemPrefix,
                    ))),
                    true
                );

                $errorCount += $itemResult->count();
                $errors = array_merge($errors, $itemResult->errors());
                $detail = array_merge($detail, $itemResult->detail());

                if ($itemResult->isPassed()) {
                    $validatedData[] = $itemResult->validatedData();
                }

                continue;
            }

            $itemResult = $this->doValidate(
                array('value' => $item),
                array('value:' . $itemPrefix => $rules),
                ValidationOptions::fromArray(array(
                    'strict' => true,
                    'reject_unknown' => false,
                    'field_prefix' => '',
                )),
                true
            );

            $errorCount += $itemResult->count();
            $errors = array_merge($errors, $itemResult->errors());
            $detail = array_merge($detail, $itemResult->detail());

            if ($itemResult->isPassed()) {
                $normalizedValue = $itemResult->validatedData();
                $validatedData[] = array_key_exists('value', $normalizedValue)
                    ? $normalizedValue['value']
                    : null;
            }
        }

        return $this->buildValidationResult($errorCount, $errors, $detail, $validatedData);
    }

    public function writeValidatedDataTo(ValidationResult $result, $target)
    {
        return $this->validatedDataWriter->write($result, $target);
    }

    public function validateAndWriteTo(array $data, array $rules, $target, array $options = array())
    {
        $result = $this->validateAndNormalize($data, $rules, $options);
        $this->writeValidatedDataTo($result, $target);

        return $result;
    }

    private function normalizeOptions(array $options)
    {
        return ValidationOptions::fromArray($options);
    }

    private function doValidate(array $data, array $rules, ValidationOptions $options, $normalizeOutput)
    {
        $errorCount = 0;
        $errors = array();
        $detail = array();
        $validatedData = array();

        if ($options->rejectUnknown()) {
            $unknownDetails = $this->unknownFieldCollector->collect(
                $data,
                $rules,
                $options->fieldPrefix()
            );

            foreach ($unknownDetails as $unknownDetail) {
                $this->appendError(
                    $errors,
                    $detail,
                    $errorCount,
                    $unknownDetail['param'],
                    $unknownDetail['value'],
                    $unknownDetail['rule'],
                    $unknownDetail['rule_value'],
                    $unknownDetail['reason']
                );
            }
        }

        foreach ($rules as $rawFieldKey => $ruleString) {
            $fieldInfo = $this->ruleParser->parseFieldRuleKey($rawFieldKey);
            $fieldPath = $fieldInfo['field'];
            $displayName = $this->pathAccessor->buildDisplayName(
                $fieldInfo['display_name'],
                $options->fieldPrefix()
            );
            $ruleItems = $this->ruleParser->parseRuleItems($ruleString);
            $valueInfo = $this->pathAccessor->getValue($data, $fieldPath, $options->strict());
            $initialExists = $valueInfo['exists'];
            $exists = $initialExists;
            $currentValue = $valueInfo['value'];

            if (!$exists && $this->ruleParser->hasRule($ruleItems, 'default')) {
                $currentValue = $this->literalValueParser->parse(
                    $this->ruleParser->findRuleArgument($ruleItems, 'default', '')
                );
                $exists = true;
            }

            $originalValue = $currentValue;
            $failed = false;

            if (!$initialExists && !$exists) {
                $failed = $this->applyMissingFieldPresenceRules(
                    $data,
                    $fieldPath,
                    $displayName,
                    $ruleItems,
                    $options,
                    $currentValue,
                    $exists,
                    $errors,
                    $detail,
                    $errorCount
                );
            } else {
                $failed = $this->applyRulesForExistingValue(
                    $data,
                    $fieldPath,
                    $displayName,
                    $ruleItems,
                    $options,
                    $currentValue,
                    $exists,
                    $errors,
                    $detail,
                    $errorCount
                );
            }

            if ($failed || !$exists) {
                continue;
            }

            if ($normalizeOutput || !$initialExists) {
                $outputValue = $currentValue;
            } else {
                $outputValue = $originalValue;
            }

            $this->pathAccessor->setValue($validatedData, $fieldPath, $outputValue);
        }

        return $this->buildValidationResult($errorCount, $errors, $detail, $validatedData);
    }

    private function applyMissingFieldPresenceRules(
        array $data,
        $fieldPath,
        $displayName,
        array $ruleItems,
        ValidationOptions $options,
        &$currentValue,
        &$exists,
        array &$errors,
        array &$detail,
        &$errorCount
    ) {
        foreach ($ruleItems as $ruleItem) {
            if ($ruleItem['key'] === 'default') {
                continue;
            }

            $definition = $this->definitionResolver->resolve($ruleItem['key']);
            if ($definition === null) {
                if ($this->isReservedPresenceRuleName($ruleItem['key'])) {
                    $this->appendError(
                        $errors,
                        $detail,
                        $errorCount,
                        $displayName,
                        null,
                        'unsupported',
                        $ruleItem['key'],
                        'rule not support'
                    );

                    return true;
                }

                continue;
            }

            if (!$definition->phase()->isPresence()) {
                continue;
            }

            $ruleResult = $this->invokeRuleHandler(
                $definition,
                $fieldPath,
                $displayName,
                false,
                $currentValue,
                $ruleItem['argument'],
                $data,
                $options
            );

            if ($ruleResult->failed()) {
                $this->appendError(
                    $errors,
                    $detail,
                    $errorCount,
                    $displayName,
                    $currentValue,
                    $ruleItem['key'],
                    $ruleItem['argument'],
                    'result: false',
                    $definition
                );

                return true;
            }

            $currentValue = $ruleResult->value();

            if ($ruleResult->shouldBreak()) {
                break;
            }
        }

        return false;
    }

    private function applyRulesForExistingValue(
        array $data,
        $fieldPath,
        $displayName,
        array $ruleItems,
        ValidationOptions $options,
        &$currentValue,
        &$exists,
        array &$errors,
        array &$detail,
        &$errorCount
    ) {
        foreach ($ruleItems as $ruleItem) {
            if ($ruleItem['key'] === 'default') {
                continue;
            }

            $definition = $this->definitionResolver->resolve($ruleItem['key']);
            if ($definition === null) {
                $this->appendError(
                    $errors,
                    $detail,
                    $errorCount,
                    $displayName,
                    $currentValue,
                    'unsupported',
                    $ruleItem['key'],
                    'rule not support'
                );

                return true;
            }

            $ruleResult = $this->invokeRuleHandler(
                $definition,
                $fieldPath,
                $displayName,
                $exists,
                $currentValue,
                $ruleItem['argument'],
                $data,
                $options
            );

            if ($ruleResult->failed()) {
                $this->appendError(
                    $errors,
                    $detail,
                    $errorCount,
                    $displayName,
                    $currentValue,
                    $ruleItem['key'],
                    $ruleItem['argument'],
                    'result: false',
                    $definition
                );

                return true;
            }

            $currentValue = $ruleResult->value();

            if ($ruleResult->shouldBreak()) {
                break;
            }
        }

        return false;
    }

    private function invokeRuleHandler(
        $definition,
        $fieldPath,
        $displayName,
        $exists,
        $currentValue,
        $ruleArgument,
        array $data,
        ValidationOptions $options
    ) {
        $handlerClass = $definition->handlerClass();
        $context = new RuleContext(
            $definition,
            $fieldPath,
            $displayName,
            $exists,
            $currentValue,
            $ruleArgument,
            $data,
            $options,
            $this,
            $this->pathAccessor
        );

        return $this->ruleResultNormalizer->normalize(
            call_user_func(array($handlerClass, 'validate'), $context),
            $currentValue
        );
    }

    private function appendError(
        array &$errors,
        array &$detail,
        &$errorCount,
        $displayName,
        $value,
        $ruleName,
        $ruleArgument,
        $reason,
        $definition = null
    ) {
        $errorCount++;
        $errors[] = $this->renderMessage($displayName, $ruleName, $ruleArgument, $definition);
        $detail[] = array(
            'param' => $displayName,
            'value' => $value,
            'rule' => $ruleName,
            'rule_value' => $ruleArgument,
            'reason' => $reason,
        );
    }

    private function renderMessage($displayName, $ruleName, $ruleArgument, $definition = null)
    {
        $messageTemplate = $this->resolveMessageTemplate($ruleName, $definition);
        $message = $messageTemplate->value();
        $message = str_replace('$paramName', $displayName, $message);
        $message = str_replace('$rule', $this->displayRuleArgument($ruleName, $ruleArgument), $message);

        return $message;
    }

    private function resolveMessageTemplate($ruleName, $definition = null)
    {
        foreach ($this->messageSources as $sourceClass) {
            if (!is_string($sourceClass) || $sourceClass === '') {
                throw new LogicException('RuleMessageSource 非法');
            }

            if (!is_subclass_of($sourceClass, RuleMessageSourceInterface::class)) {
                throw new LogicException('RuleMessageSource 未实现接口：' . $sourceClass);
            }

            $template = $sourceClass::resolve($ruleName);
            if ($template !== null) {
                return $template;
            }
        }

        if ($definition !== null && $definition->defaultMessageValue() instanceof MessageTemplate) {
            return $definition->defaultMessageValue();
        }

        return MessageTemplate::of($this->fallbackTemplateValue($ruleName));
    }

    private function fallbackTemplateValue($ruleName)
    {
        $fallbackTemplates = array(
            'unknown' => '$paramName is unknown',
            'unsupported' => '$paramName rule is unsupported: $rule',
            'array' => '$paramName must be array',
        );

        return array_key_exists($ruleName, $fallbackTemplates)
            ? $fallbackTemplates[$ruleName]
            : '$paramName validate failed';
    }

    private function displayRuleArgument($ruleName, $ruleArgument)
    {
        if (!in_array($ruleName, array(
            'gtField',
            'egtField',
            'ltField',
            'eltField',
            'timeAfterField',
            'timeAfterOrEqualField',
            'timeBeforeField',
            'timeBeforeOrEqualField',
        ), true)) {
            return (string)$ruleArgument;
        }

        $parts = explode(',', (string)$ruleArgument, 2);

        return isset($parts[1]) ? $parts[1] : (isset($parts[0]) ? $parts[0] : '');
    }

    private function buildValidationResult($errorCount, array $errors, array $detail, $validatedData)
    {
        if ((int)$errorCount === 0) {
            return ValidationResult::success($validatedData);
        }

        return ValidationResult::failure($errors, $detail, $validatedData);
    }

    private function isReservedPresenceRuleName($ruleName)
    {
        return in_array($ruleName, array(
            'required',
            'default',
            'requiredIf',
            'requiredWithout',
            'prohibitedWith',
            'prohibitedUnless',
        ), true);
    }
}
