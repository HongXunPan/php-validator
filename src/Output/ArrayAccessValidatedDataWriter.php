<?php

namespace HongXunPan\Validator\Output;

use ArrayAccess;
use HongXunPan\Validator\Exception\InvalidValidatedDataTargetException;
use HongXunPan\Validator\Result\ValidationResult;

class ArrayAccessValidatedDataWriter implements ValidatedDataWriterInterface
{
    public function write(ValidationResult $result, $target)
    {
        if (!($target instanceof ArrayAccess)) {
            throw new InvalidValidatedDataTargetException('validated_data 目标必须实现 ArrayAccess');
        }

        foreach ((array)$result->validatedData() as $key => $value) {
            $target[$key] = $value;
        }

        return $target;
    }
}
