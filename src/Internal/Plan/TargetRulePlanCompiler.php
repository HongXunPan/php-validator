<?php

namespace HongXunPan\Validator\Internal\Plan;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Internal\Input\DeclaredTargetTreeBuilder;
use HongXunPan\Validator\Internal\Parsing\RuleStringParser;
use HongXunPan\Validator\Internal\Rules\ResolvedRule;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Rule\ConditionalPresenceRuleInterface;
use HongXunPan\Validator\Rule\DependentValueRuleInterface;
use HongXunPan\Validator\Rule\PresenceRuleInterface;
use HongXunPan\Validator\Rule\ValueMaterializationRuleInterface;

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

    public function __construct(RuleStringParser $ruleStringParser, RuleSet $ruleSet, DeclaredTargetTreeBuilder $declaredTargetTreeBuilder)
    {
        $this->ruleStringParser = $ruleStringParser;
        $this->ruleSet = $ruleSet;
        $this->declaredTargetTreeBuilder = $declaredTargetTreeBuilder;
    }

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

    public function compileStandalone($fieldPath, $displayName, $ruleString)
    {
        return $this->compileRuleTargetAndRuleString(
            new RuleTarget($fieldPath, $displayName),
            $ruleString
        );
    }

    private function compileRuleTargetAndRuleString(RuleTarget $ruleTarget, $ruleString)
    {
        $ruleItems = $this->ruleStringParser->parseRuleItems($ruleString);

        $unsupportedRules = array();
        $materializationRules = array();
        $conditionalPresenceRules = array();
        $presenceRules = array();
        $localValueRules = array();
        $dependentValueRules = array();

        foreach ($ruleItems as $ruleItem) {
            $resolvedRule = $this->ruleSet->resolveRule($ruleItem->inputRuleKey());
            $compiledRule = new CompiledRule(
                $ruleItem,
                $resolvedRule,
                $this->resolveStage($resolvedRule)
            );

            switch ($compiledRule->stage()) {
                case CompiledRule::STAGE_UNSUPPORTED:
                    $unsupportedRules[] = $compiledRule;
                    break;
                case CompiledRule::STAGE_MATERIALIZATION:
                    $materializationRules[] = $compiledRule;
                    break;
                case CompiledRule::STAGE_CONDITIONAL_PRESENCE:
                    $conditionalPresenceRules[] = $compiledRule;
                    break;
                case CompiledRule::STAGE_PRESENCE:
                    $presenceRules[] = $compiledRule;
                    break;
                case CompiledRule::STAGE_DEPENDENT_VALUE:
                    $dependentValueRules[] = $compiledRule;
                    break;
                default:
                    $localValueRules[] = $compiledRule;
                    break;
            }
        }

        return new CompiledTargetRulePlan(
            $ruleTarget,
            $unsupportedRules,
            $materializationRules,
            $conditionalPresenceRules,
            $presenceRules,
            $localValueRules,
            $dependentValueRules
        );
    }

    private function resolveStage($resolvedRule)
    {
        if (!$resolvedRule instanceof ResolvedRule) {
            return CompiledRule::STAGE_UNSUPPORTED;
        }

        $ruleClass = $resolvedRule->ruleClass();

        if (is_subclass_of($ruleClass, ValueMaterializationRuleInterface::class)) {
            return CompiledRule::STAGE_MATERIALIZATION;
        }

        if (is_subclass_of($ruleClass, ConditionalPresenceRuleInterface::class)) {
            return CompiledRule::STAGE_CONDITIONAL_PRESENCE;
        }

        if (is_subclass_of($ruleClass, PresenceRuleInterface::class)) {
            return CompiledRule::STAGE_PRESENCE;
        }

        if (is_subclass_of($ruleClass, DependentValueRuleInterface::class)) {
            return CompiledRule::STAGE_DEPENDENT_VALUE;
        }

        return CompiledRule::STAGE_LOCAL_VALUE;
    }
}
