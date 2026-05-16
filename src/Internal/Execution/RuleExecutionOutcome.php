<?php

namespace HongXunPan\Validator\Internal\Execution;

use HongXunPan\Validator\Internal\Rules\ResolvedRule;
use HongXunPan\Validator\Result\RuleResult;

/**
 * @phpstan-consistent-constructor
 */
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

    /**
     * @return static
     */
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

    /**
     * @param ResolvedRule $resolvedRule
     * @param RuleResult $ruleResult
     *
     * @return static
     */
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

    /**
     * @param ResolvedRule $resolvedRule
     * @param string $messageTemplate
     * @param RuleResult $ruleResult
     *
     * @return static
     */
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

    /**
     * @return bool
     */
    public function isSkipped()
    {
        return $this->status === self::STATUS_SKIPPED;
    }

    /**
     * @return bool
     */
    public function isPassed()
    {
        return $this->status === self::STATUS_PASSED;
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * @return bool
     */
    public function isUnsupported()
    {
        return $this->failureReason === self::REASON_UNSUPPORTED;
    }

    /**
     * @return string|null
     */
    public function failureReason()
    {
        return $this->failureReason;
    }

    /**
     * @return ResolvedRule|null
     */
    public function resolvedRule()
    {
        return $this->resolvedRule;
    }

    /**
     * @return string|null
     */
    public function messageTemplate()
    {
        return $this->messageTemplate;
    }

    /**
     * @return RuleResult|null
     */
    public function ruleResult()
    {
        return $this->ruleResult;
    }

    /**
     * @return bool
     */
    public function shouldBreak()
    {
        return $this->ruleResult !== null
            && $this->ruleResult->shouldBreak();
    }
}
