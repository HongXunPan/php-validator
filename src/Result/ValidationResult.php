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

    /**
     * @param int $errorCount
     * @param array<int, string> $errors
     * @param array<int, array<string, mixed>> $detail
     * @param mixed $validatedData
     */
    public function __construct($errorCount, array $errors, array $detail, $validatedData)
    {
        $this->errorCount = (int)$errorCount;
        $this->errors = $errors;
        $this->detail = $detail;
        $this->validatedData = $validatedData;
    }

    /**
     * @param mixed $validatedData
     *
     * @return static
     */
    public static function success($validatedData)
    {
        return new static(0, array(), array(), $validatedData);
    }

    /**
     * @param array<int, string> $errors
     * @param array<int, array<string, mixed>> $detail
     * @param mixed $validatedData
     *
     * @return static
     */
    public static function failure(array $errors, array $detail, $validatedData)
    {
        return new static(count($errors), $errors, $detail, $validatedData);
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->errorCount;
    }

    /**
     * @return array<int, string>
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function detail()
    {
        return $this->detail;
    }

    /**
     * @return mixed
     */
    public function validatedData()
    {
        return $this->validatedData;
    }

    /**
     * @return bool
     */
    public function isPassed()
    {
        return $this->errorCount === 0;
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        return !$this->isPassed();
    }

    /**
     * @return array<string, mixed>
     */
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
