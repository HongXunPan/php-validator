<?php

namespace HongXunPan\Validator\Internal\Rules;

class ResolvedRule
{
    /**
     * @var string
     */
    private $inputRuleKey;
    /**
     * @var string
     */
    private $finalRuleKey;
    /**
     * @var string
     */
    private $ruleClass;

    public function __construct($inputRuleKey, $finalRuleKey, $ruleClass)
    {
        $this->inputRuleKey = (string)$inputRuleKey;
        $this->finalRuleKey = (string)$finalRuleKey;
        $this->ruleClass = $ruleClass;
    }

    public function inputRuleKey()
    {
        return $this->inputRuleKey;
    }

    public function finalRuleKey()
    {
        return $this->finalRuleKey;
    }

    public function ruleClass()
    {
        return $this->ruleClass;
    }
}
