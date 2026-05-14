<?php

namespace HongXunPan\Validator\Support;

class RuleParser
{
    public function parseFieldRuleKey($rawKey)
    {
        $parts = explode(':', (string)$rawKey, 2);

        return array(
            'field' => $parts[0],
            'display_name' => isset($parts[1]) ? $parts[1] : $parts[0],
        );
    }

    public function parseRuleItems($ruleString)
    {
        $rawItems = explode('|', (string)$ruleString);
        $parsedItems = array();

        foreach ($rawItems as $rawItem) {
            $rawItem = trim($rawItem);
            if ($rawItem === '') {
                continue;
            }

            $parsedRule = $this->parseRuleItem($rawItem);
            $parsedItems[] = array(
                'raw' => $rawItem,
                'key' => $parsedRule['key'],
                'argument' => $parsedRule['argument'],
            );
        }

        return $parsedItems;
    }

    public function parseRuleItem($rawRule)
    {
        $parts = explode(':', (string)$rawRule, 2);

        return array(
            'key' => $parts[0],
            'argument' => isset($parts[1]) ? $parts[1] : '',
        );
    }

    public function hasRule(array $ruleItems, $targetRule)
    {
        foreach ($ruleItems as $ruleItem) {
            if (isset($ruleItem['key']) && $ruleItem['key'] === $targetRule) {
                return true;
            }
        }

        return false;
    }

    public function findRuleArgument(array $ruleItems, $targetRule, $defaultValue = null)
    {
        foreach ($ruleItems as $ruleItem) {
            if (isset($ruleItem['key']) && $ruleItem['key'] === $targetRule) {
                return isset($ruleItem['argument']) ? $ruleItem['argument'] : $defaultValue;
            }
        }

        return $defaultValue;
    }
}
