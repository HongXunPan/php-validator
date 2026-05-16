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

    /**
     * @param bool $rawExists
     * @param mixed $rawValue
     */
    public function __construct($rawExists, $rawValue)
    {
        $this->rawExists = (bool)$rawExists;
        $this->rawValue = $rawValue;
        $this->currentExists = (bool)$rawExists;
        $this->currentValue = $rawValue;
    }

    /**
     * @return bool
     */
    public function rawExists()
    {
        return $this->rawExists;
    }

    /**
     * @return mixed
     */
    public function rawValue()
    {
        return $this->rawValue;
    }

    /**
     * @return bool
     */
    public function currentExists()
    {
        return $this->currentExists;
    }

    /**
     * @return mixed
     */
    public function currentValue()
    {
        return $this->currentValue;
    }

    /**
     * @param RuleResult $ruleResult
     */
    public function applyRuleResult(RuleResult $ruleResult)
    {
        if ($ruleResult->exists() !== null) {
            $this->currentExists = $ruleResult->exists();
        }

        $this->currentValue = $ruleResult->value();
    }

    /**
     * @return void
     */
    public function useCurrentAsMaterialized()
    {
        $this->materialized = true;
        $this->materializedExists = $this->currentExists;
        $this->materializedValue = $this->currentValue;
    }

    /**
     * @return bool
     */
    public function isMaterialized()
    {
        return $this->materialized;
    }

    /**
     * @return bool
     */
    public function materializedExists()
    {
        return $this->materializedExists;
    }

    /**
     * @return mixed
     */
    public function materializedValue()
    {
        return $this->materializedValue;
    }

    /**
     * @return void
     */
    public function markDependentReadable()
    {
        $this->dependentReadable = true;
    }

    /**
     * @return bool
     */
    public function isDependentReadable()
    {
        return $this->dependentReadable;
    }

    /**
     * @return void
     */
    public function markFailed()
    {
        $this->failed = true;
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        return $this->failed;
    }

    /**
     * @return void
     */
    public function skipValueValidation()
    {
        $this->skipValueValidation = true;
    }

    /**
     * @return bool
     */
    public function shouldSkipValueValidation()
    {
        return $this->skipValueValidation;
    }

    /**
     * @param bool $normalizeOutput
     *
     * @return void
     */
    public function commitOutputValue($normalizeOutput)
    {
        $this->outputCommitted = true;

        if ($normalizeOutput || !$this->rawExists) {
            $this->outputValue = $this->currentValue;

            return;
        }

        $this->outputValue = $this->rawValue;
    }

    /**
     * @return bool
     */
    public function hasOutputValue()
    {
        return $this->outputCommitted;
    }

    /**
     * @return mixed
     */
    public function outputValue()
    {
        return $this->outputValue;
    }
}
