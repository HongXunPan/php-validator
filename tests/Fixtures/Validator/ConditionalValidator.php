<?php

namespace HongXunPan\Validator\Tests\Fixtures\Validator;

use HongXunPan\Validator\Tests\Fixtures\Rule\NullableIfTestRule;
use HongXunPan\Validator\Tests\Fixtures\Rule\RequiredIfTestRule;
use HongXunPan\Validator\Validator;

class ConditionalValidator extends Validator
{
    /**
     * @var array<string, string>
     */
    protected static $extraRules = array(
        'nullableIfTest' => NullableIfTestRule::class,
        'requiredIfTest' => RequiredIfTestRule::class,
    );
}
