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

    /**
     * @param bool $passed
     * @param mixed $value
     * @param bool $shouldBreak
     * @param bool|null $exists
     */
    private function __construct($passed, $value, $shouldBreak, $exists)
    {
        $this->passed = (bool)$passed;
        $this->value = $value;
        $this->shouldBreak = (bool)$shouldBreak;
        $this->exists = $exists === null ? null : (bool)$exists;
    }

    /**
     * @param mixed $value
     * @param bool|null $exists
     *
     * @return static
     */
    public static function pass($value, $exists = null)
    {
        return new static(true, $value, false, $exists);
    }

    /**
     * @param mixed $value
     * @param bool|null $exists
     *
     * @return static
     */
    public static function fail($value, $exists = null)
    {
        return new static(false, $value, false, $exists);
    }

    /**
     * @param mixed $value
     * @param bool|null $exists
     *
     * @return static
     */
    public static function passAndBreak($value, $exists = null)
    {
        return new static(true, $value, true, $exists);
    }

    /**
     * @return bool
     */
    public function passed()
    {
        return $this->passed;
    }

    /**
     * @return bool
     */
    public function failed()
    {
        return !$this->passed;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function shouldBreak()
    {
        return $this->shouldBreak;
    }

    /**
     * @return bool|null
     */
    public function exists()
    {
        return $this->exists;
    }
}
