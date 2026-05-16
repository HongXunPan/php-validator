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

    /**
     * @param string $inputRuleKey
     * @param string $rawArgument
     */
    public function __construct($inputRuleKey, $rawArgument)
    {
        $this->inputRuleKey = (string)$inputRuleKey;
        $this->rawArgument = (string)$rawArgument;
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
    public function rawArgument()
    {
        return $this->rawArgument;
    }
}
