<?php

namespace HongXunPan\Validator\Context;

interface RuleValueReaderInterface
{
    /**
     * @param string $fieldPath
     * @param bool $strict
     *
     * @return PathValue
     */
    public function rawPathValue($fieldPath, $strict);

    /**
     * @param string $fieldPath
     *
     * @return PathValue
     */
    public function materializedPathValue($fieldPath);

    /**
     * @param string $fieldPath
     *
     * @return PathValue
     */
    public function dependentPathValue($fieldPath);
}
