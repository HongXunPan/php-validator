<?php

namespace HongXunPan\Validator\Internal\Plan;

use HongXunPan\Validator\Internal\Target\RuleTarget;

class CompiledTargetRulePlan
{
    /**
     * @var RuleTarget
     */
    private $ruleTarget;
    /**
     * @var CompiledRule[]
     */
    private $unsupportedRules;
    /**
     * @var CompiledRule[]
     */
    private $missingValueCreationRules;
    /**
     * @var CompiledRule[]
     */
    private $presentValueNormalizationRules;
    /**
     * @var CompiledRule[]
     */
    private $fieldPresenceAssertionRules;
    /**
     * @var CompiledRule[]
     */
    private $presentValueGuardRules;
    /**
     * @var CompiledRule[]
     */
    private $presentValueTransformRules;
    /**
     * @var CompiledRule[]
     */
    private $presentValueAssertionRules;
    /**
     * @var CompiledRule[]
     */
    private $crossFieldAssertionRules;

    /**
     * @param RuleTarget $ruleTarget
     * @param array<int, CompiledRule> $unsupportedRules
     * @param array<int, CompiledRule> $missingValueCreationRules
     * @param array<int, CompiledRule> $presentValueNormalizationRules
     * @param array<int, CompiledRule> $fieldPresenceAssertionRules
     * @param array<int, CompiledRule> $presentValueGuardRules
     * @param array<int, CompiledRule> $presentValueTransformRules
     * @param array<int, CompiledRule> $presentValueAssertionRules
     * @param array<int, CompiledRule> $crossFieldAssertionRules
     */
    public function __construct(
        RuleTarget $ruleTarget,
        array $unsupportedRules,
        array $missingValueCreationRules,
        array $presentValueNormalizationRules,
        array $fieldPresenceAssertionRules,
        array $presentValueGuardRules,
        array $presentValueTransformRules,
        array $presentValueAssertionRules,
        array $crossFieldAssertionRules
    ) {
        $this->ruleTarget = $ruleTarget;
        $this->unsupportedRules = $unsupportedRules;
        $this->missingValueCreationRules = $missingValueCreationRules;
        $this->presentValueNormalizationRules = $presentValueNormalizationRules;
        $this->fieldPresenceAssertionRules = $fieldPresenceAssertionRules;
        $this->presentValueGuardRules = $presentValueGuardRules;
        $this->presentValueTransformRules = $presentValueTransformRules;
        $this->presentValueAssertionRules = $presentValueAssertionRules;
        $this->crossFieldAssertionRules = $crossFieldAssertionRules;
    }

    /**
     * @return RuleTarget
     */
    public function ruleTarget()
    {
        return $this->ruleTarget;
    }

    /**
     * @return array<int, CompiledRule>
     */
    public function unsupportedRules()
    {
        return $this->unsupportedRules;
    }

    /**
     * @return array<int, CompiledRule>
     */
    public function missingValueCreationRules()
    {
        return $this->missingValueCreationRules;
    }

    /**
     * @return array<int, CompiledRule>
     */
    public function presentValueNormalizationRules()
    {
        return $this->presentValueNormalizationRules;
    }

    /**
     * @return array<int, CompiledRule>
     */
    public function fieldPresenceAssertionRules()
    {
        return $this->fieldPresenceAssertionRules;
    }

    /**
     * @return array<int, CompiledRule>
     */
    public function presentValueGuardRules()
    {
        return $this->presentValueGuardRules;
    }

    /**
     * @return array<int, CompiledRule>
     */
    public function presentValueTransformRules()
    {
        return $this->presentValueTransformRules;
    }

    /**
     * @return array<int, CompiledRule>
     */
    public function presentValueAssertionRules()
    {
        return $this->presentValueAssertionRules;
    }

    /**
     * @return array<int, CompiledRule>
     */
    public function crossFieldAssertionRules()
    {
        return $this->crossFieldAssertionRules;
    }

    /**
     * @return bool
     */
    public function hasCrossFieldAssertionRules()
    {
        return !empty($this->crossFieldAssertionRules);
    }

    /**
     * @return bool
     */
    public function hasUnsupportedRules()
    {
        return !empty($this->unsupportedRules);
    }

    /**
     * @param RuleTarget $ruleTarget
     *
     * @return self
     */
    public function withRuleTarget(RuleTarget $ruleTarget)
    {
        return new self(
            $ruleTarget,
            $this->unsupportedRules,
            $this->missingValueCreationRules,
            $this->presentValueNormalizationRules,
            $this->fieldPresenceAssertionRules,
            $this->presentValueGuardRules,
            $this->presentValueTransformRules,
            $this->presentValueAssertionRules,
            $this->crossFieldAssertionRules
        );
    }
}
