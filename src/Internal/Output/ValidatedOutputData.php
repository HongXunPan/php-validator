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

    /**
     * @param PathAccessor $pathAccessor
     */
    public function __construct(PathAccessor $pathAccessor)
    {
        $this->pathAccessor = $pathAccessor;
    }

    /**
     * @param RuleTarget $ruleTarget
     * @param TargetValueContext $targetValueContext
     *
     * @return void
     */
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

    /**
     * @param mixed $value
     *
     * @return void
     */
    public function appendItem($value)
    {
        $this->data[] = $value;
    }

    /**
     * @param ValidatedOutputData $validatedOutputData
     *
     * @return void
     */
    public function appendOutputData(ValidatedOutputData $validatedOutputData)
    {
        $this->appendItem($validatedOutputData->toArray());
    }

    /**
     * @param string $fieldPath
     *
     * @return \HongXunPan\Validator\Context\PathValue
     */
    public function pathValue($fieldPath)
    {
        return $this->pathAccessor->getValue($this->data, $fieldPath, true);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function toArray()
    {
        return $this->data;
    }
}
