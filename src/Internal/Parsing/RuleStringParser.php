<?php

namespace HongXunPan\Validator\Internal\Parsing;

use HongXunPan\Validator\Internal\Target\RuleTarget;

class RuleStringParser
{
    /**
     * @param string $rawKey
     *
     * @return RuleTarget
     */
    public function parseTargetKey($rawKey)
    {
        $parts = explode(':', (string)$rawKey, 2);

        return new RuleTarget(
            $parts[0],
            isset($parts[1]) ? $parts[1] : $parts[0]
        );
    }

    /**
     * @param string $ruleString
     *
     * @return array<int, ParsedRuleToken>
     */
    public function parseRuleItems($ruleString)
    {
        $rawItems = explode('|', (string)$ruleString);
        $parsedItems = array();

        foreach ($rawItems as $rawItem) {
            $rawItem = trim($rawItem);
            if ($rawItem === '') {
                continue;
            }

            $parsedItems[] = $this->parseRuleItem($rawItem);
        }

        return $parsedItems;
    }

    /**
     * @param string $rawRule
     *
     * @return ParsedRuleToken
     */
    public function parseRuleItem($rawRule)
    {
        $parts = explode(':', (string)$rawRule, 2);

        return new ParsedRuleToken(
            $parts[0],
            isset($parts[1]) ? $parts[1] : ''
        );
    }

    /**
     * @param array<int, ParsedRuleToken> $ruleItems
     * @param string $targetRule
     *
     * @return bool
     */
    public function hasRule(array $ruleItems, $targetRule)
    {
        foreach ($ruleItems as $ruleItem) {
            if ($ruleItem instanceof ParsedRuleToken && $ruleItem->inputRuleKey() === $targetRule) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, ParsedRuleToken> $ruleItems
     * @param string $targetRule
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function findRuleArgument(array $ruleItems, $targetRule, $defaultValue = null)
    {
        foreach ($ruleItems as $ruleItem) {
            if ($ruleItem instanceof ParsedRuleToken && $ruleItem->inputRuleKey() === $targetRule) {
                return $ruleItem->rawArgument();
            }
        }

        return $defaultValue;
    }
}
