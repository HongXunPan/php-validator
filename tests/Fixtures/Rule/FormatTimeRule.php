<?php

namespace HongXunPan\Validator\Tests\Fixtures\Rule;

use HongXunPan\Validator\Rule\AbstractRule;
use HongXunPan\Validator\Rule\Marker\TimeRule;

class FormatTimeRule extends AbstractRule implements TimeRule
{
    const KEY = 'formatTime';
}
