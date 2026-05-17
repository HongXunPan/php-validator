<?php

namespace HongXunPan\Validator\Internal\Plan;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Internal\Input\DeclaredTargetTreeBuilder;
use HongXunPan\Validator\Internal\Parsing\RuleStringParser;
use HongXunPan\Validator\Internal\Rules\ResolvedRule;
use HongXunPan\Validator\Internal\Rules\RuleArchetypeInspector;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Rule\Argument\RuleArgumentParserInterface;

class TargetRulePlanCompiler
{
    /**
     * @var RuleStringParser
     */
    private $ruleStringParser;
    /**
     * @var RuleSet
     */
    private $ruleSet;
    /**
     * @var DeclaredTargetTreeBuilder
     */
    private $declaredTargetTreeBuilder;

    /**
     * @param RuleStringParser $ruleStringParser
     * @param RuleSet $ruleSet
     * @param DeclaredTargetTreeBuilder $declaredTargetTreeBuilder
     */
    public function __construct(RuleStringParser $ruleStringParser, RuleSet $ruleSet, DeclaredTargetTreeBuilder $declaredTargetTreeBuilder)
    {
        $this->ruleStringParser = $ruleStringParser;
        $this->ruleSet = $ruleSet;
        $this->declaredTargetTreeBuilder = $declaredTargetTreeBuilder;
    }

    /**
     * @param array<string, string> $rules
     *
     * @return CompiledValidationPlan
     */
    public function compile(array $rules)
    {
        $targetPlans = array();
        $pathLabelMap = new PathLabelMap();

        foreach ($rules as $rawTargetKey => $ruleString) {
            $ruleTarget = $this->ruleStringParser->parseTargetKey($rawTargetKey);
            $targetPlan = $this->compileRuleTargetAndRuleString($ruleTarget, $ruleString);
            $targetPlans[] = $targetPlan;
            $pathLabelMap->register($ruleTarget->fieldPath(), $ruleTarget->displayName());
        }

        return new CompiledValidationPlan(
            $targetPlans,
            $pathLabelMap,
            $this->declaredTargetTreeBuilder->build($targetPlans)
        );
    }

    /**
     * @param string $fieldPath
     * @param string $displayName
     * @param string $ruleString
     *
     * @return CompiledTargetRulePlan
     */
    public function compileStandalone($fieldPath, $displayName, $ruleString)
    {
        return $this->compileRuleTargetAndRuleString(
            new RuleTarget($fieldPath, $displayName),
            $ruleString
        );
    }

    /**
     * @param RuleTarget $ruleTarget
     * @param string $ruleString
     *
     * @return CompiledTargetRulePlan
     */
    private function compileRuleTargetAndRuleString(RuleTarget $ruleTarget, $ruleString)
    {
        $ruleItems = $this->ruleStringParser->parseRuleItems($ruleString);

        $unsupportedRules = array();
        $missingValueCreationRules = array();
        $presentValueNormalizationRules = array();
        $fieldPresenceAssertionRules = array();
        $presentValueGuardRules = array();
        $presentValueTransformRules = array();
        $presentValueAssertionRules = array();
        $crossFieldAssertionRules = array();

        foreach ($ruleItems as $ruleItem) {
            $resolvedRule = $this->ruleSet->resolveRule($ruleItem->inputRuleKey());
            $compiledRule = new CompiledRule(
                $ruleItem,
                $resolvedRule,
                $this->resolveStage($resolvedRule),
                $this->parseRuleArgument($resolvedRule, $ruleItem->rawArgument()),
                $this->resolveArgumentParserClass($resolvedRule)
            );

            switch ($compiledRule->stage()) {
                case CompiledRule::STAGE_UNSUPPORTED:
                    $unsupportedRules[] = $compiledRule;
                    break;
                case CompiledRule::STAGE_PREPARE_MISSING_VALUE:
                    $missingValueCreationRules[] = $compiledRule;
                    break;
                case CompiledRule::STAGE_PREPARE_PRESENT_VALUE:
                    $presentValueNormalizationRules[] = $compiledRule;
                    break;
                case CompiledRule::STAGE_ASSERT_FIELD_PRESENCE:
                    $fieldPresenceAssertionRules[] = $compiledRule;
                    break;
                case CompiledRule::STAGE_GUARD_PRESENT_VALUE:
                    $presentValueGuardRules[] = $compiledRule;
                    break;
                case CompiledRule::STAGE_TRANSFORM_PRESENT_VALUE:
                    $presentValueTransformRules[] = $compiledRule;
                    break;
                case CompiledRule::STAGE_ASSERT_CROSS_FIELD_VALUE:
                    $crossFieldAssertionRules[] = $compiledRule;
                    break;
                default:
                    $presentValueAssertionRules[] = $compiledRule;
                    break;
            }
        }

        return new CompiledTargetRulePlan(
            $ruleTarget,
            $unsupportedRules,
            $missingValueCreationRules,
            $presentValueNormalizationRules,
            $fieldPresenceAssertionRules,
            $presentValueGuardRules,
            $presentValueTransformRules,
            $presentValueAssertionRules,
            $crossFieldAssertionRules
        );
    }

    /**
     * @param ResolvedRule|null $resolvedRule
     *
     * @return string
     */
    private function resolveStage($resolvedRule)
    {
        if (!$resolvedRule instanceof ResolvedRule) {
            return CompiledRule::STAGE_UNSUPPORTED;
        }

        $stage = RuleArchetypeInspector::resolveCompiledStage($resolvedRule->ruleClass());

        return $stage === null
            ? CompiledRule::STAGE_UNSUPPORTED
            : $stage;
    }

    /**
     * @param ResolvedRule|null $resolvedRule
     * @param string $rawArgument
     *
     * @return mixed
     */
    private function parseRuleArgument($resolvedRule, $rawArgument)
    {
        if (!$resolvedRule instanceof ResolvedRule) {
            return null;
        }

        $parserClass = $this->resolveArgumentParserClass($resolvedRule);
        if (!is_string($parserClass) || $parserClass === '') {
            throw new InvalidRuleArgumentException('规则参数解析器非法：' . $resolvedRule->ruleClass());
        }

        if (!class_exists($parserClass)) {
            throw new InvalidRuleArgumentException('规则参数解析器不存在：' . $parserClass);
        }

        $parser = new $parserClass();
        if (!$parser instanceof RuleArgumentParserInterface) {
            throw new InvalidRuleArgumentException('规则参数解析器未实现契约：' . $parserClass);
        }

        return $parser->parse($rawArgument);
    }

    /**
     * @param ResolvedRule|null $resolvedRule
     *
     * @return string|null
     */
    private function resolveArgumentParserClass($resolvedRule)
    {
        if (!$resolvedRule instanceof ResolvedRule) {
            return null;
        }

        return call_user_func(array($resolvedRule->ruleClass(), 'argumentParserClass'));
    }
}
