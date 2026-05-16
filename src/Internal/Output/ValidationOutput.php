<?php

namespace HongXunPan\Validator\Internal\Output;

use HongXunPan\Validator\Internal\Detail\ValidationDetailItem;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Result\ValidationResult;

class ValidationOutput
{
    /**
     * @var PathAccessor
     */
    private $pathAccessor;
    /**
     * @var array
     */
    private $errors = array();
    /**
     * @var ValidationDetailItem[]
     */
    private $detailItems = array();
    /**
     * @var ValidatedOutputData
     */
    private $validatedOutputData;

    /**
     * @param PathAccessor $pathAccessor
     */
    public function __construct(PathAccessor $pathAccessor)
    {
        $this->pathAccessor = $pathAccessor;
        $this->validatedOutputData = new ValidatedOutputData($pathAccessor);
    }

    /**
     * @param string $message
     * @param ValidationDetailItem $detailItem
     */
    public function appendFailure($message, ValidationDetailItem $detailItem)
    {
        $this->errors[] = (string)$message;
        $this->detailItems[] = $detailItem;
    }

    /**
     * @param RuleTarget $ruleTarget
     * @param TargetValueContext $targetValueContext
     */
    public function writeValidatedTarget(RuleTarget $ruleTarget, TargetValueContext $targetValueContext)
    {
        $this->validatedOutputData->writeTarget($ruleTarget, $targetValueContext);
    }

    /**
     * @return bool
     */
    public function isPassed()
    {
        return empty($this->errors);
    }

    /**
     * @return array<int, string>
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * @return array<int, ValidationDetailItem>
     */
    public function detailItems()
    {
        return $this->detailItems;
    }

    /**
     * @return ValidatedOutputData
     */
    public function validatedOutputData()
    {
        return $this->validatedOutputData;
    }

    /**
     * @param string $fieldPath
     *
     * @return mixed
     */
    public function validatedPathValue($fieldPath)
    {
        return $this->validatedOutputData->pathValue($fieldPath);
    }

    /**
     * @return ValidationResult
     */
    public function toValidationResult()
    {
        if (empty($this->errors)) {
            return ValidationResult::success($this->validatedOutputData->toArray());
        }

        return ValidationResult::failure(
            $this->errors,
            $this->detailArray(),
            $this->validatedOutputData->toArray()
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function detailArray()
    {
        $detail = array();

        foreach ($this->detailItems as $detailItem) {
            $detail[] = $detailItem->toArray();
        }

        return $detail;
    }
}
