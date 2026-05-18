<?php

namespace HongXunPan\Validator\Rule\Collection;

class ProhibitedKeysRule extends AbstractArrayKeyRule
{
    const KEY = 'prohibitedKeys';
    const MESSAGE = '$paramName must not contain prohibited keys $rule';

    protected static function matchesKeys(array $value, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $value)) {
                return false;
            }
        }

        return true;
    }
}
