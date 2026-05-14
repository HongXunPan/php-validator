<?php

namespace HongXunPan\Validator\Support;

class LiteralValueParser
{
    public function parse($value)
    {
        $decoded = json_decode((string)$value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $value;
    }
}
