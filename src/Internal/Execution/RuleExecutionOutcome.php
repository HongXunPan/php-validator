<?php

namespace HongXunPan\Validator\Internal\Execution;

use HongXunPan\Validator\Internal\Rules\ResolvedRule;
use HongXunPan\Validator\Result\RuleResult;

class RuleExecutionOutcome
{
    /**
     * @var string
     */
    private $status;
    /**
     * @var string|null
     */
    private $failureReason;
    /**
     * @var ResolvedRule|null
     */
    private $resolvedRule;
    /**
     * @var string|null
     */
    private $messageTemplate;
    /**
     * @var RuleResult|null
     */
    private $ruleResult;

    private function __construct($status, $failureReason, $resolvedRule, $messageTemplate, $ruleResult)
    {
        $this->status = $status;
        $this->failureReason = $failureReason;
        $this->resolvedRule = $resolvedRule;
        $this->messageTemplate = $messageTemplate;
        $this->ruleResult = $ruleResult;
    }

    public static function skipped()
    {
        return new static(RuleExecutionStatus::SKIPPED, null, null, null, null);
    }

    public static function unsupported()
    {
        return new static(
            RuleExecutionStatus::FAILED,
            RuleExecutionFailureReason::UNSUPPORTED,
            null,
            null,
            null
        );
    }

    public static function passed(ResolvedRule $resolvedRule, RuleResult $ruleResult)
    {
        return new static(
            RuleExecutionStatus::PASSED,
            null,
            $resolvedRule,
            null,
            $ruleResult
        );
    }

    public static function failed(ResolvedRule $resolvedRule, $messageTemplate, RuleResult $ruleResult)
    {
        return new static(
            RuleExecutionStatus::FAILED,
            RuleExecutionFailureReason::RULE_FAILED,
            $resolvedRule,
            $messageTemplate,
            $ruleResult
        );
    }

    public function isSkipped()
    {
        return $this->status === RuleExecutionStatus::SKIPPED;
    }

    public function isPassed()
    {
        return $this->status === RuleExecutionStatus::PASSED;
    }

    public function isFailed()
    {
        return $this->status === RuleExecutionStatus::FAILED;
    }

    public function isUnsupported()
    {
        return $this->failureReason === RuleExecutionFailureReason::UNSUPPORTED;
    }

    public function failureReason()
    {
        return $this->failureReason;
    }

    public function resolvedRule()
    {
        return $this->resolvedRule;
    }

    public function messageTemplate()
    {
        return $this->messageTemplate;
    }

    public function ruleResult()
    {
        return $this->ruleResult;
    }

    public function shouldBreak()
    {
        return $this->ruleResult !== null
            && $this->ruleResult->shouldBreak();
    }
}
