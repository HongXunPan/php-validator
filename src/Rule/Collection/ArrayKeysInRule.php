<?php

namespace HongXunPan\Validator\Rule\Collection;

class ArrayKeysInRule extends AbstractArrayKeyRule
{
    const KEY = 'arrayKeysIn';
    const MESSAGE = '$paramName keys must be in $rule';

    protected static function matchesKeys(array $value, array $keys)
    {
        foreach (array_keys($value) as $key) {
            if (!in_array((string)$key, $keys, true)) {
                return false;
            }
        }

        return true;
    }
}
