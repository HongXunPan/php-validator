<?php

namespace HongXunPan\Validator\Internal\Output;

use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Internal\Context\TargetValueContext;

class ValidatedOutputData
{
    /**
     * @var PathAccessor
     */
    private $pathAccessor;
    /**
     * @var array
     */
    private $data = array();

    public function __construct(PathAccessor $pathAccessor)
    {
        $this->pathAccessor = $pathAccessor;
    }

    public function writeTarget(RuleTarget $ruleTarget, TargetValueContext $targetValueContext)
    {
        if (!$targetValueContext->hasOutputValue()) {
            return;
        }

        $this->pathAccessor->setValue(
            $this->data,
            $ruleTarget->fieldPath(),
            $targetValueContext->outputValue()
        );
    }

    public function appendItem($value)
    {
        $this->data[] = $value;
    }

    public function appendOutputData(ValidatedOutputData $validatedOutputData)
    {
        $this->appendItem($validatedOutputData->toArray());
    }

    public function pathValue($fieldPath)
    {
        return $this->pathAccessor->getValue($this->data, $fieldPath, true);
    }

    public function toArray()
    {
        return $this->data;
    }
}
