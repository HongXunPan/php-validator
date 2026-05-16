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

    /**
     * @param ValidationOutput $validationOutput
     * @param string $itemPath
     */
    public function __construct(ValidationOutput $validationOutput, $itemPath)
    {
        $this->validationOutput = $validationOutput;
        $this->itemPath = (string)$itemPath;
    }

    /**
     * @return array<int, string>
     */
    public function errors()
    {
        return $this->validationOutput->errors();
    }

    /**
     * @return array<int, \HongXunPan\Validator\Internal\Detail\ValidationDetailItem>
     */
    public function detailItems()
    {
        return $this->validationOutput->detailItems();
    }

    /**
     * @return bool
     */
    public function isPassed()
    {
        return $this->validationOutput->isPassed();
    }

    /**
     * @return \HongXunPan\Validator\Context\PathValue
     */
    public function normalizedValue()
    {
        return $this->validationOutput->validatedPathValue($this->itemPath);
    }
}
