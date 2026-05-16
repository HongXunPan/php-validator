<?php

namespace HongXunPan\Validator\Internal\Output;

use HongXunPan\Validator\Internal\Detail\ValidationDetailItem;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Result\ValidationResult;
use HongXunPan\Validator\Rule\Type\ArrayType;

class ListValidationOutput
{
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
     * @var ValidationMessageRenderer
     */
    private $messageRenderer;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->validatedOutputData = new ValidatedOutputData(new PathAccessor());
        $this->messageRenderer = new ValidationMessageRenderer();
    }

    /**
     * @param string $itemPrefix
     * @param mixed $item
     *
     * @return void
     */
    public function addListItemTypeError($itemPrefix, $item)
    {
        $detailItem = ValidationDetailItem::listItemNotArray($itemPrefix, $item);
        $this->errors[] = $this->messageRenderer->render(
            ArrayType::defaultMessage(),
            $detailItem->param(),
            $detailItem->ruleValue()
        );
        $this->detailItems[] = $detailItem;
    }

    /**
     * @param ValidationOutput $itemOutput
     *
     * @return void
     */
    public function mergeObjectOutput(ValidationOutput $itemOutput)
    {
        $this->errors = array_merge($this->errors, $itemOutput->errors());
        $this->detailItems = array_merge($this->detailItems, $itemOutput->detailItems());

        if ($itemOutput->isPassed()) {
            $this->validatedOutputData->appendOutputData($itemOutput->validatedOutputData());
        }
    }

    /**
     * @param ScalarValidationOutput $itemOutput
     *
     * @return void
     */
    public function mergeScalarOutput(ScalarValidationOutput $itemOutput)
    {
        $this->errors = array_merge($this->errors, $itemOutput->errors());
        $this->detailItems = array_merge($this->detailItems, $itemOutput->detailItems());

        if ($itemOutput->isPassed()) {
            $value = $itemOutput->normalizedValue();
            $this->validatedOutputData->appendItem($value->exists() ? $value->value() : null);
        }
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
