<?php

namespace HongXunPan\Validator\Output;

use HongXunPan\Validator\Result\ValidationResult;

interface ValidatedDataWriterInterface
{
    /**
     * @param mixed $target
     *
     * @return mixed
     */
    public function write(ValidationResult $result, $target);
}
