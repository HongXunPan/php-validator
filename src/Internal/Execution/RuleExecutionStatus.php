<?php

namespace HongXunPan\Validator\Internal\Execution;

final class RuleExecutionStatus
{
    const SKIPPED = 'skipped';
    const PASSED = 'passed';
    const FAILED = 'failed';

    private function __construct()
    {
    }
}
