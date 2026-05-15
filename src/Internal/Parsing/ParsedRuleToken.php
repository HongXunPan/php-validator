<?php

namespace HongXunPan\Validator\Internal\Parsing;

class ParsedRuleToken
{
    /**
     * @var string
     */
    private $inputRuleKey;
    /**
     * @var string
     */
    private $rawArgument;

    public function __construct($inputRuleKey, $rawArgument)
    {
        $this->inputRuleKey = (string)$inputRuleKey;
        $this->rawArgument = (string)$rawArgument;
    }

    public function inputRuleKey()
    {
        return $this->inputRuleKey;
    }

    public function rawArgument()
    {
        return $this->rawArgument;
    }
}
