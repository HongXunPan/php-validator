<?php

namespace HongXunPan\Validator\Internal\Plan;

use HongXunPan\Validator\Internal\Parsing\ParsedRuleToken;
use HongXunPan\Validator\Internal\Rules\ResolvedRule;

class CompiledRule
{
    const STAGE_UNSUPPORTED = 'unsupported';
    const STAGE_MATERIALIZATION = 'materialization';
    const STAGE_CONDITIONAL_PRESENCE = 'conditional_presence';
    const STAGE_PRESENCE = 'presence';
    const STAGE_LOCAL_VALUE = 'local_value';
    const STAGE_DEPENDENT_VALUE = 'dependent_value';

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
     * @param ParsedRuleToken $parsedRule
     * @param ResolvedRule|null $resolvedRule
     * @param string $stage
     */
    public function __construct(ParsedRuleToken $parsedRule, $resolvedRule, $stage)
    {
        $this->parsedRule = $parsedRule;
        $this->resolvedRule = $resolvedRule;
        $this->stage = (string)$stage;
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
     * @return bool
     */
    public function isUnsupported()
    {
        return $this->stage === self::STAGE_UNSUPPORTED;
    }
}
