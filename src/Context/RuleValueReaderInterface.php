<?php

namespace HongXunPan\Validator\Context;

interface RuleValueReaderInterface
{
    public function rawPathValue($fieldPath, $strict);

    public function materializedPathValue($fieldPath);

    public function dependentPathValue($fieldPath);
}
