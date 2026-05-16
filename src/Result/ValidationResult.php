<?php

namespace HongXunPan\Validator\Result;

/**
 * ValidationResult 承接对外统一结果。
 *
 * @phpstan-consistent-constructor
 */
class ValidationResult
{
    /**
     * @var int
     */
    private $errorCount;
    /**
     * @var array
     */
    private $errors;
    /**
     * @var array
     */
    private $detail;
    /**
     * @var mixed
     */
    private $validatedData;

    public function __construct($errorCount, array $errors, array $detail, $validatedData)
    {
        $this->errorCount = (int)$errorCount;
        $this->errors = $errors;
        $this->detail = $detail;
        $this->validatedData = $validatedData;
    }

    public static function success($validatedData)
    {
        return new static(0, array(), array(), $validatedData);
    }

    public static function failure(array $errors, array $detail, $validatedData)
    {
        return new static(count($errors), $errors, $detail, $validatedData);
    }

    public function count()
    {
        return $this->errorCount;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function detail()
    {
        return $this->detail;
    }

    public function validatedData()
    {
        return $this->validatedData;
    }

    public function isPassed()
    {
        return $this->errorCount === 0;
    }

    public function isFailed()
    {
        return !$this->isPassed();
    }

    public function toArray()
    {
        return array(
            'count' => $this->errorCount,
            'errors' => $this->errors,
            'detail' => $this->detail,
            'validated_data' => $this->validatedData,
        );
    }
}
