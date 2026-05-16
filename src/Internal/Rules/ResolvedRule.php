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

    /**
     * @param string $inputRuleKey
     * @param string $finalRuleKey
     * @param string $ruleClass
     */
    public function __construct($inputRuleKey, $finalRuleKey, $ruleClass)
    {
        $this->inputRuleKey = (string)$inputRuleKey;
        $this->finalRuleKey = (string)$finalRuleKey;
        $this->ruleClass = $ruleClass;
    }

    /**
     * @return string
     */
    public function inputRuleKey()
    {
        return $this->inputRuleKey;
    }

    /**
     * @return string
     */
    public function finalRuleKey()
    {
        return $this->finalRuleKey;
    }

    /**
     * @return string
     */
    public function ruleClass()
    {
        return $this->ruleClass;
    }
}
