<?php

namespace HongXunPan\Validator\Result;

/**
 * RuleResult 承接单条规则执行结果。
 *
 * @phpstan-consistent-constructor
 */
class RuleResult
{
    /**
     * @var bool
     */
    private $passed;
    /**
     * @var mixed
     */
    private $value;
    /**
     * @var bool
     */
    private $shouldBreak;
    /**
     * @var bool|null
     */
    private $exists;

    private function __construct($passed, $value, $shouldBreak, $exists)
    {
        $this->passed = (bool)$passed;
        $this->value = $value;
        $this->shouldBreak = (bool)$shouldBreak;
        $this->exists = $exists === null ? null : (bool)$exists;
    }

    public static function pass($value, $exists = null)
    {
        return new static(true, $value, false, $exists);
    }

    public static function fail($value, $exists = null)
    {
        return new static(false, $value, false, $exists);
    }

    public static function passAndBreak($value, $exists = null)
    {
        return new static(true, $value, true, $exists);
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

    public function exists()
    {
        return $this->exists;
    }
}
