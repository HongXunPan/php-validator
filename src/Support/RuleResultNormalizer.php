<?php

namespace HongXunPan\Validator\Support;

use HongXunPan\Validator\Result\RuleResult;

class RuleResultNormalizer
{
    public function normalize($result, $currentValue)
    {
        if ($result instanceof RuleResult) {
            return $result;
        }

        if (is_array($result) && array_key_exists('passed', $result)) {
            $passed = (bool)$result['passed'];
            $value = array_key_exists('value', $result) ? $result['value'] : $currentValue;
            $shouldBreak = !empty($result['break']);

            if ($passed && $shouldBreak) {
                return RuleResult::passAndBreak($value);
            }

            if ($passed) {
                return RuleResult::pass($value);
            }

            return RuleResult::fail($value);
        }

        if ((bool)$result) {
            return RuleResult::pass($currentValue);
        }

        return RuleResult::fail($currentValue);
    }
}
