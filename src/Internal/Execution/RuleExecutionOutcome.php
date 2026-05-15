<?php

namespace HongXunPan\Validator\Internal\Execution;

use HongXunPan\Validator\Internal\Rules\ResolvedRule;
use HongXunPan\Validator\Result\RuleResult;

class RuleExecutionOutcome
{
    const STATUS_SKIPPED = 'skipped';
    const STATUS_PASSED = 'passed';
    const STATUS_FAILED = 'failed';

    const REASON_UNSUPPORTED = 'unsupported';
    const REASON_RULE_FAILED = 'rule_failed';

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
        return new static(self::STATUS_SKIPPED, null, null, null, null);
    }

    public static function unsupported()
    {
        return new static(
            self::STATUS_FAILED,
            self::REASON_UNSUPPORTED,
            null,
            null,
            null
        );
    }

    public static function passed(ResolvedRule $resolvedRule, RuleResult $ruleResult)
    {
        return new static(
            self::STATUS_PASSED,
            null,
            $resolvedRule,
            null,
            $ruleResult
        );
    }

    public static function failed(ResolvedRule $resolvedRule, $messageTemplate, RuleResult $ruleResult)
    {
        return new static(
            self::STATUS_FAILED,
            self::REASON_RULE_FAILED,
            $resolvedRule,
            $messageTemplate,
            $ruleResult
        );
    }

    public function isSkipped()
    {
        return $this->status === self::STATUS_SKIPPED;
    }

    public function isPassed()
    {
        return $this->status === self::STATUS_PASSED;
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isUnsupported()
    {
        return $this->failureReason === self::REASON_UNSUPPORTED;
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
