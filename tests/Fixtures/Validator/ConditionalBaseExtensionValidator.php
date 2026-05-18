<?php

namespace HongXunPan\Validator\Tests\Fixtures\Validator;

use HongXunPan\Validator\Tests\Fixtures\Rule\NullableIfReferencedPresentTestRule;
use HongXunPan\Validator\Tests\Fixtures\Rule\RequiredIfReferencedEqTestRule;
use HongXunPan\Validator\Validator;

class ConditionalBaseExtensionValidator extends Validator
{
    /**
     * @var array<string, string>
     */
    protected static $extraRules = array(
        'nullableIfReferencedPresentTest' => NullableIfReferencedPresentTestRule::class,
        'requiredIfReferencedEqTest' => RequiredIfReferencedEqTestRule::class,
    );
}
