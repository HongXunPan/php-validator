<?php

namespace HongXunPan\Validator\Tests\Fixtures\Context;

use HongXunPan\Validator\Context\PathValue;
use HongXunPan\Validator\Context\RuleValueReaderInterface;

class StubRuleValueReader implements RuleValueReaderInterface
{
    /**
     * @var array
     */
    private $rawValues;
    /**
     * @var array
     */
    private $materializedValues;
    /**
     * @var array
     */
    private $dependentValues;

    public function __construct(array $rawValues = array(), array $materializedValues = array(), array $dependentValues = array())
    {
        $this->rawValues = $rawValues;
        $this->materializedValues = $materializedValues;
        $this->dependentValues = $dependentValues;
    }

    public function rawPathValue($fieldPath, $strict)
    {
        return $this->toPathValue($this->rawValues, $fieldPath);
    }

    public function materializedPathValue($fieldPath)
    {
        return $this->toPathValue($this->materializedValues, $fieldPath);
    }

    public function dependentPathValue($fieldPath)
    {
        return $this->toPathValue($this->dependentValues, $fieldPath);
    }

    /**
     * @param array $values
     * @param string $fieldPath
     *
     * @return PathValue
     */
    private function toPathValue(array $values, $fieldPath)
    {
        if (!array_key_exists($fieldPath, $values)) {
            return new PathValue(false, null);
        }

        $value = $values[$fieldPath];
        if ($value instanceof PathValue) {
            return $value;
        }

        return new PathValue(true, $value);
    }
}
