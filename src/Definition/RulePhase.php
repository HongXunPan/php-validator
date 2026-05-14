<?php

namespace HongXunPan\Validator\Definition;

use InvalidArgumentException;

/**
 * RulePhase 承接规则阶段。
 */
class RulePhase
{
    const PRESENCE = 'presence';
    const VALUE = 'value';

    private $value;

    public function __construct($value)
    {
        if (!in_array($value, array(self::PRESENCE, self::VALUE), true)) {
            throw new InvalidArgumentException('RulePhase 不支持：' . $value);
        }

        $this->value = $value;
    }

    public static function presence()
    {
        return new static(self::PRESENCE);
    }

    public static function value()
    {
        return new static(self::VALUE);
    }

    public function valueString()
    {
        return $this->value;
    }

    public function isPresence()
    {
        return $this->value === self::PRESENCE;
    }

    public function isValue()
    {
        return $this->value === self::VALUE;
    }
}
