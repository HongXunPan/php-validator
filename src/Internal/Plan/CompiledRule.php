<?php

namespace HongXunPan\Validator\Internal\Plan;

use HongXunPan\Validator\Internal\Parsing\ParsedRuleToken;
use HongXunPan\Validator\Internal\Rules\ResolvedRule;

class CompiledRule
{
    const STAGE_UNSUPPORTED = 'unsupported';
    const STAGE_PREPARE_MISSING_VALUE = 'prepare_missing_value';
    const STAGE_PREPARE_PRESENT_VALUE = 'prepare_present_value';
    const STAGE_ASSERT_FIELD_PRESENCE = 'assert_field_presence';
    const STAGE_GUARD_PRESENT_VALUE = 'guard_present_value';
    const STAGE_TRANSFORM_PRESENT_VALUE = 'transform_present_value';
    const STAGE_ASSERT_PRESENT_VALUE = 'assert_present_value';
    const STAGE_ASSERT_CROSS_FIELD_VALUE = 'assert_cross_field_value';

    /**
     * @var ParsedRuleToken
     */
    private $parsedRule;
    /**
     * @var ResolvedRule|null
     */
    private $resolvedRule;
    /**
     * @var string
     */
    private $stage;
    /**
     * @var mixed
     */
    private $parsedArgument;
    /**
     * @var string|null
     */
    private $argumentParserClass;

    /**
     * @param ParsedRuleToken $parsedRule
     * @param ResolvedRule|null $resolvedRule
     * @param string $stage
     * @param mixed $parsedArgument
     * @param string|null $argumentParserClass
     */
    public function __construct(ParsedRuleToken $parsedRule, $resolvedRule, $stage, $parsedArgument = null, $argumentParserClass = null)
    {
        $this->parsedRule = $parsedRule;
        $this->resolvedRule = $resolvedRule;
        $this->stage = (string)$stage;
        $this->parsedArgument = $parsedArgument;
        $this->argumentParserClass = $argumentParserClass === null ? null : (string)$argumentParserClass;
    }

    /**
     * @return ParsedRuleToken
     */
    public function parsedRule()
    {
        return $this->parsedRule;
    }

    /**
     * @return ResolvedRule|null
     */
    public function resolvedRule()
    {
        return $this->resolvedRule;
    }

    /**
     * @return string
     */
    public function stage()
    {
        return $this->stage;
    }

    /**
     * @return mixed
     */
    public function parsedArgument()
    {
        return $this->parsedArgument;
    }

    /**
     * @return string|null
     */
    public function argumentParserClass()
    {
        return $this->argumentParserClass;
    }

    /**
     * @return bool
     */
    public function isUnsupported()
    {
        return $this->stage === self::STAGE_UNSUPPORTED;
    }
}
