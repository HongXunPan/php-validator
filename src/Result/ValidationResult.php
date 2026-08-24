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
     * 返回失败明细中的稳定字段路径，并按首次出现顺序去重。
     *
     * @return array<int, string>
     */
    public function failedFieldPaths()
    {
        $fieldPaths = array();
        $seen = array();

        foreach ($this->detail as $item) {
            if (!is_array($item) || !array_key_exists('field_path', $item)) {
                continue;
            }

            $fieldPath = $item['field_path'];
            if (!is_string($fieldPath) || $fieldPath === '' || array_key_exists($fieldPath, $seen)) {
                continue;
            }

            $fieldPaths[] = $fieldPath;
            $seen[$fieldPath] = true;
        }

        return $fieldPaths;
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
