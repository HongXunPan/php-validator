<?php

namespace HongXunPan\Validator\Internal\Field;

use HongXunPan\Validator\Result\RuleResult;

class TargetState
{
    /**
     * @var bool
     */
    private $initialExists;
    /**
     * @var bool
     */
    private $exists;
    /**
     * @var mixed
     */
    private $originalValue;
    /**
     * @var mixed
     */
    private $value;

    public function __construct($exists, $value)
    {
        $this->initialExists = (bool)$exists;
        $this->exists = (bool)$exists;
        $this->originalValue = $value;
        $this->value = $value;
    }

    public function initialExists()
    {
        return $this->initialExists;
    }

    public function exists()
    {
        return $this->exists;
    }

    public function value()
    {
        return $this->value;
    }

    public function originalValue()
    {
        return $this->originalValue;
    }

    public function apply(RuleResult $ruleResult)
    {
        if ($ruleResult->exists() !== null) {
            $this->exists = $ruleResult->exists();
        }

        $this->value = $ruleResult->value();
    }

    public function outputValue($normalizeOutput)
    {
        if ($normalizeOutput || !$this->initialExists) {
            return $this->value;
        }

        return $this->originalValue;
    }
}
