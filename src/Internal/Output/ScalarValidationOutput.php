<?php

namespace HongXunPan\Validator\Internal\Output;

class ScalarValidationOutput
{
    /**
     * @var ValidationOutput
     */
    private $validationOutput;
    /**
     * @var string
     */
    private $itemPath;

    public function __construct(ValidationOutput $validationOutput, $itemPath)
    {
        $this->validationOutput = $validationOutput;
        $this->itemPath = (string)$itemPath;
    }

    public function errors()
    {
        return $this->validationOutput->errors();
    }

    public function detailItems()
    {
        return $this->validationOutput->detailItems();
    }

    public function isPassed()
    {
        return $this->validationOutput->isPassed();
    }

    public function normalizedValue()
    {
        return $this->validationOutput->validatedPathValue($this->itemPath);
    }
}
