<?php

namespace HongXunPan\Validator\Support;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class StrictLiteralParser
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function parse($value)
    {
        $rawValue = (string)$value;
        $decoded = json_decode($rawValue, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        throw new InvalidRuleArgumentException('规则参数 literal 必须是合法 JSON literal：' . $rawValue);
    }
}
