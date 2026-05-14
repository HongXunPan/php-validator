<?php

namespace HongXunPan\Validator\Result;

/**
 * RuleResult 承接单条规则执行结果。
 */
class RuleResult
{
    private $passed;
    private $value;
    private $shouldBreak;

    private function __construct($passed, $value, $shouldBreak)
    {
        $this->passed = (bool)$passed;
        $this->value = $value;
        $this->shouldBreak = (bool)$shouldBreak;
    }

    public static function pass($value)
    {
        return new static(true, $value, false);
    }

    public static function fail($value)
    {
        return new static(false, $value, false);
    }

    public static function passAndBreak($value)
    {
        return new static(true, $value, true);
    }

    public function passed()
    {
        return $this->passed;
    }

    public function failed()
    {
        return !$this->passed;
    }

    public function value()
    {
        return $this->value;
    }

    public function shouldBreak()
    {
        return $this->shouldBreak;
    }
}
