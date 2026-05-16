<?php

namespace HongXunPan\Validator\Internal\Rules;

use HongXunPan\Validator\Exception\RuleNameReservedException;
use HongXunPan\Validator\Exception\ValidatorException;

class RuleAliasMap
{
    /**
     * @var array
     */
    private $aliasMap = array();

    /**
     * @param array<string, string> $aliases
     * @param RuleRegistry $ruleRegistry
     *
     * @return void
     */
    public function registerAliases(array $aliases, RuleRegistry $ruleRegistry)
    {
        foreach ($aliases as $aliasKey => $finalRuleKey) {
            $aliasKey = (string)$aliasKey;
            $finalRuleKey = (string)$finalRuleKey;

            if ($ruleRegistry->hasRule($aliasKey)) {
                throw new RuleNameReservedException('alias key 与真实规则名冲突：' . $aliasKey);
            }

            if ($this->hasAlias($aliasKey)) {
                throw new ValidatorException('alias 重复：' . $aliasKey);
            }

            if (!$ruleRegistry->hasRule($finalRuleKey)) {
                throw new ValidatorException('alias 指向不存在的规则：' . $finalRuleKey);
            }

            $this->aliasMap[$aliasKey] = $finalRuleKey;
        }
    }

    /**
     * @param string $aliasKey
     *
     * @return bool
     */
    public function hasAlias($aliasKey)
    {
        return array_key_exists((string)$aliasKey, $this->aliasMap);
    }

    /**
     * @param string $aliasKey
     *
     * @return string|null
     */
    public function finalRuleKey($aliasKey)
    {
        $aliasKey = (string)$aliasKey;
        if (!$this->hasAlias($aliasKey)) {
            return null;
        }

        return $this->aliasMap[$aliasKey];
    }
}
