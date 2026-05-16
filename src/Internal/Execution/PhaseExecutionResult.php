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

    /**
     * @return self
     */
    public static function failed()
    {
        return new self(self::STATUS_FAILED);
    }

    /**
     * @return self
     */
    public static function broken()
    {
        return new self(self::STATUS_BROKEN);
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * @return bool
     */
    public function isBroken()
    {
        return $this->status === self::STATUS_BROKEN;
    }
}
