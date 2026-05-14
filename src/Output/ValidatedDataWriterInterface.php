<?php

namespace HongXunPan\Validator\Output;

use HongXunPan\Validator\Result\ValidationResult;

interface ValidatedDataWriterInterface
{
    public function write(ValidationResult $result, $target);
}
