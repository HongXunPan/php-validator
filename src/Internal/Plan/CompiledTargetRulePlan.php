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

    public function ruleTarget()
    {
        return $this->ruleTarget;
    }

    public function unsupportedRules()
    {
        return $this->unsupportedRules;
    }

    public function materializationRules()
    {
        return $this->materializationRules;
    }

    public function conditionalPresenceRules()
    {
        return $this->conditionalPresenceRules;
    }

    public function presenceRules()
    {
        return $this->presenceRules;
    }

    public function localValueRules()
    {
        return $this->localValueRules;
    }

    public function dependentValueRules()
    {
        return $this->dependentValueRules;
    }

    public function hasDependentValueRules()
    {
        return !empty($this->dependentValueRules);
    }

    public function hasUnsupportedRules()
    {
        return !empty($this->unsupportedRules);
    }
}
