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

    public function __construct(ParsedRuleToken $parsedRule, $resolvedRule, $stage)
    {
        $this->parsedRule = $parsedRule;
        $this->resolvedRule = $resolvedRule;
        $this->stage = (string)$stage;
    }

    public function parsedRule()
    {
        return $this->parsedRule;
    }

    public function resolvedRule()
    {
        return $this->resolvedRule;
    }

    public function stage()
    {
        return $this->stage;
    }

    public function isUnsupported()
    {
        return $this->stage === self::STAGE_UNSUPPORTED;
    }
}
