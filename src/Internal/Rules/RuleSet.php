<?php

namespace HongXunPan\Validator\Internal\Rules;

/**
 * @phpstan-consistent-constructor
 */
class RuleSet
{
    /**
     * @var RuleRegistry
     */
    private $ruleRegistry;
    /**
     * @var RuleAliasMap
     */
    private $ruleAliasMap;
    /**
     * @var RuleMessageCatalog
     */
    private $ruleMessageCatalog;

    /**
     * @param RuleRegistry $ruleRegistry
     * @param RuleAliasMap $ruleAliasMap
     * @param RuleMessageCatalog $ruleMessageCatalog
     */
    public function __construct(RuleRegistry $ruleRegistry, RuleAliasMap $ruleAliasMap, RuleMessageCatalog $ruleMessageCatalog)
    {
        $this->ruleRegistry = $ruleRegistry;
        $this->ruleAliasMap = $ruleAliasMap;
        $this->ruleMessageCatalog = $ruleMessageCatalog;
    }

    /**
     * @param string $validatorClass
     *
     * @return self
     */
    public static function fromValidatorClass($validatorClass)
    {
        $ruleRegistry = new RuleRegistry();
        $ruleRegistry->registerCoreRules(call_user_func(array($validatorClass, 'coreRules')));
        $ruleRegistry->registerExtraRules(call_user_func(array($validatorClass, 'extraRules')));

        $ruleAliasMap = new RuleAliasMap();
        $ruleAliasMap->registerAliases(call_user_func(array($validatorClass, 'ruleAliases')), $ruleRegistry);

        $ruleMessageCatalog = new RuleMessageCatalog();
        $ruleMessageCatalog->registerMessages(call_user_func(array($validatorClass, 'ruleMessages')));

        return new self($ruleRegistry, $ruleAliasMap, $ruleMessageCatalog);
    }

    /**
     * @param string $inputRuleKey
     *
     * @return ResolvedRule|null
     */
    public function resolveRule($inputRuleKey)
    {
        $inputRuleKey = (string)$inputRuleKey;

        if ($this->ruleRegistry->hasRule($inputRuleKey)) {
            return new ResolvedRule($inputRuleKey, $inputRuleKey, $this->ruleRegistry->ruleClass($inputRuleKey));
        }

        $finalRuleKey = $this->ruleAliasMap->finalRuleKey($inputRuleKey);
        if ($finalRuleKey === null) {
            return null;
        }

        return new ResolvedRule($inputRuleKey, $finalRuleKey, $this->ruleRegistry->ruleClass($finalRuleKey));
    }

    /**
     * @param ResolvedRule $resolvedRule
     *
     * @return string
     */
    public function resolveMessage(ResolvedRule $resolvedRule)
    {
        return $this->ruleMessageCatalog->resolveMessage($resolvedRule);
    }
}
