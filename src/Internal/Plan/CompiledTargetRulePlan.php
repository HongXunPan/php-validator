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
    private $materializationRules;
    /**
     * @var CompiledRule[]
     */
    private $conditionalPresenceRules;
    /**
     * @var CompiledRule[]
     */
    private $presenceRules;
    /**
     * @var CompiledRule[]
     */
    private $localValueRules;
    /**
     * @var CompiledRule[]
     */
    private $dependentValueRules;

    /**
     * @param RuleTarget $ruleTarget
     * @param array<int, CompiledRule> $unsupportedRules
     * @param array<int, CompiledRule> $materializationRules
     * @param array<int, CompiledRule> $conditionalPresenceRules
     * @param array<int, CompiledRule> $presenceRules
     * @param array<int, CompiledRule> $localValueRules
     * @param array<int, CompiledRule> $dependentValueRules
     */
    public function __construct(
        RuleTarget $ruleTarget,
        array $unsupportedRules,
        array $materializationRules,
        array $conditionalPresenceRules,
        array $presenceRules,
        array $localValueRules,
        array $dependentValueRules
    ) {
        $this->ruleTarget = $ruleTarget;
        $this->unsupportedRules = $unsupportedRules;
        $this->materializationRules = $materializationRules;
        $this->conditionalPresenceRules = $conditionalPresenceRules;
        $this->presenceRules = $presenceRules;
        $this->localValueRules = $localValueRules;
        $this->dependentValueRules = $dependentValueRules;
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
    public function materializationRules()
    {
        return $this->materializationRules;
    }

    /**
     * @return array<int, CompiledRule>
     */
    public function conditionalPresenceRules()
    {
        return $this->conditionalPresenceRules;
    }

    /**
     * @return array<int, CompiledRule>
     */
    public function presenceRules()
    {
        return $this->presenceRules;
    }

    /**
     * @return array<int, CompiledRule>
     */
    public function localValueRules()
    {
        return $this->localValueRules;
    }

    /**
     * @return array<int, CompiledRule>
     */
    public function dependentValueRules()
    {
        return $this->dependentValueRules;
    }

    /**
     * @return bool
     */
    public function hasDependentValueRules()
    {
        return !empty($this->dependentValueRules);
    }

    /**
     * @return bool
     */
    public function hasUnsupportedRules()
    {
        return !empty($this->unsupportedRules);
    }
}
