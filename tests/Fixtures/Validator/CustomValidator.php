<?php

namespace HongXunPan\Validator\Tests\Fixtures\Validator;

use HongXunPan\Validator\Validator;
use HongXunPan\Validator\Tests\Fixtures\Rule\MinLengthTestRule;
use HongXunPan\Validator\Tests\Fixtures\Rule\TrimTestRule;

class CustomValidator extends Validator
{
    /**
     * @var array<string, string>
     */
    protected static $extraRules = array(
        'trimTest' => TrimTestRule::class,
        'minLengthTest' => MinLengthTestRule::class,
    );

    /**
     * @var array<string, string>
     */
    protected static $ruleAliases = array(
        'trimAlias' => 'trimTest',
        'minAlias' => 'minLengthTest',
    );

    /**
     * @var array<string, string>
     */
    protected static $ruleMessages = array(
        'minLengthTest' => '$paramName 长度太短',
    );
}
