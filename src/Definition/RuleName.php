<?php

namespace HongXunPan\Validator\Definition;

use InvalidArgumentException;

/**
 * RuleName 承接规则名。
 */
class RuleName
{
    private $value;

    public function __construct($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            throw new InvalidArgumentException('RuleName 不能为空');
        }

        $this->value = $value;
    }

    public static function of($value)
    {
        if ($value instanceof self) {
            return $value;
        }

        return new static($value);
    }

    public function value()
    {
        return $this->value;
    }

    public function equals($other)
    {
        $other = static::of($other);

        return $this->value === $other->value();
    }

    public function __toString()
    {
        return $this->value;
    }
}
