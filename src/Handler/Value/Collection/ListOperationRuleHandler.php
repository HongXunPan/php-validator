<?php

namespace HongXunPan\Validator\Handler\Value\Collection;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;
use HongXunPan\Validator\Rule\Marker\ListRule;

class ListOperationRuleHandler implements ValueRuleHandlerInterface, ListRule
{
    public static function validate($context)
    {
        $value = $context->value();
        $ruleName = $context->definition()->name()->value();
        $argument = (string)$context->ruleArgument();

        if ($ruleName === 'arrayCountMax' && !is_array($value)) {
            return true;
        }

        if (!is_array($value)) {
            return false;
        }

        switch ($ruleName) {
            case 'distinct':
                return array(
                    'passed' => true,
                    'value' => static::uniqueValues($value),
                );
            case 'sortAsc':
                $sorted = $value;
                sort($sorted);

                return array(
                    'passed' => true,
                    'value' => $sorted,
                );
            case 'minItems':
                return count($value) >= (int)$argument;
            case 'maxItems':
            case 'arrayCountMax':
                return count($value) <= (int)$argument;
        }

        return false;
    }

    private static function uniqueValues(array $values)
    {
        $uniqueValues = array();

        foreach ($values as $value) {
            if (in_array($value, $uniqueValues, true)) {
                continue;
            }

            $uniqueValues[] = $value;
        }

        return array_values($uniqueValues);
    }
}
