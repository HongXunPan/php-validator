<?php

namespace HongXunPan\Validator\Internal\Context;

use HongXunPan\Validator\Result\RuleResult;

class TargetValueContext
{
    /**
     * @var bool
     */
    private $rawExists;
    /**
     * @var mixed
     */
    private $rawValue;
    /**
     * @var bool
     */
    private $currentExists;
    /**
     * @var mixed
     */
    private $currentValue;
    /**
     * @var bool
     */
    private $materialized = false;
    /**
     * @var bool
     */
    private $materializedExists = false;
    /**
     * @var mixed
     */
    private $materializedValue;
    /**
     * @var bool
     */
    private $dependentReadable = false;
    /**
     * @var bool
     */
    private $failed = false;
    /**
     * @var bool
     */
    private $skipValueValidation = false;
    /**
     * @var bool
     */
    private $outputCommitted = false;
    /**
     * @var mixed
     */
    private $outputValue;

    public function __construct($rawExists, $rawValue)
    {
        $this->rawExists = (bool)$rawExists;
        $this->rawValue = $rawValue;
        $this->currentExists = (bool)$rawExists;
        $this->currentValue = $rawValue;
    }

    public function rawExists()
    {
        return $this->rawExists;
    }

    public function rawValue()
    {
        return $this->rawValue;
    }

    public function currentExists()
    {
        return $this->currentExists;
    }

    public function currentValue()
    {
        return $this->currentValue;
    }

    public function applyRuleResult(RuleResult $ruleResult)
    {
        if ($ruleResult->exists() !== null) {
            $this->currentExists = $ruleResult->exists();
        }

        $this->currentValue = $ruleResult->value();
    }

    public function useCurrentAsMaterialized()
    {
        $this->materialized = true;
        $this->materializedExists = $this->currentExists;
        $this->materializedValue = $this->currentValue;
    }

    public function isMaterialized()
    {
        return $this->materialized;
    }

    public function materializedExists()
    {
        return $this->materializedExists;
    }

    public function materializedValue()
    {
        return $this->materializedValue;
    }

    public function markDependentReadable()
    {
        $this->dependentReadable = true;
    }

    public function isDependentReadable()
    {
        return $this->dependentReadable;
    }

    public function markFailed()
    {
        $this->failed = true;
    }

    public function isFailed()
    {
        return $this->failed;
    }

    public function skipValueValidation()
    {
        $this->skipValueValidation = true;
    }

    public function shouldSkipValueValidation()
    {
        return $this->skipValueValidation;
    }

    public function commitOutputValue($normalizeOutput)
    {
        $this->outputCommitted = true;

        if ($normalizeOutput || !$this->rawExists) {
            $this->outputValue = $this->currentValue;

            return;
        }

        $this->outputValue = $this->rawValue;
    }

    public function hasOutputValue()
    {
        return $this->outputCommitted;
    }

    public function outputValue()
    {
        return $this->outputValue;
    }
}
