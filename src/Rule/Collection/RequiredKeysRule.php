<?php

namespace HongXunPan\Validator\Rule\Collection;

class RequiredKeysRule extends AbstractArrayKeyRule
{
    const KEY = 'requiredKeys';
    const MESSAGE = '$paramName must contain required keys $rule';

    protected static function matchesKeys(array $value, array $keys)
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $value)) {
                return false;
            }
        }

        return true;
    }
}
