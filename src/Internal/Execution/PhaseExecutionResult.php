<?php

namespace HongXunPan\Validator\Internal\Execution;

final class PhaseExecutionResult
{
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_BROKEN = 'broken';

    /**
     * @var string
     */
    private $status;

    private function __construct($status)
    {
        $this->status = (string)$status;
    }

    public static function completed()
    {
        return new self(self::STATUS_COMPLETED);
    }

    public static function failed()
    {
        return new self(self::STATUS_FAILED);
    }

    public static function broken()
    {
        return new self(self::STATUS_BROKEN);
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isBroken()
    {
        return $this->status === self::STATUS_BROKEN;
    }
}
