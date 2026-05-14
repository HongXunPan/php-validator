<?php

namespace HongXunPan\Validator\Handler\Value\Collection;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;

class LegacyNonNegativeIntListUniqueSortedRuleHandler implements ValueRuleHandlerInterface
{
    public static function validate($context)
    {
        $value = $context->value();
        if (!is_array($value)) {
            return false;
        }

        $kernel = $context->kernel();
        if ($kernel === null) {
            return false;
        }

        $result = $kernel->validateListAndNormalize($value, 'nonNegativeInt');
        if ($result->isFailed()) {
            return false;
        }

        $normalized = static::uniqueValues($result->validatedData());
        sort($normalized);

        return array(
            'passed' => true,
            'value' => array_values($normalized),
        );
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

        return $uniqueValues;
    }
}
