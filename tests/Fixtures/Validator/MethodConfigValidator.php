<?php

namespace HongXunPan\Validator\Tests\Fixtures\Validator;

use HongXunPan\Validator\Tests\Fixtures\Rule\MinLengthTestRule;
use HongXunPan\Validator\Tests\Fixtures\Rule\TrimTestRule;
use HongXunPan\Validator\Validator;

/**
 * @noinspection PhpMissingReturnTypeInspection
 */
class MethodConfigValidator extends Validator
{
    /**
     * @return array<string, string>
     */
    protected static function defineExtraRules()
    {
        return array(
            'trimTest' => TrimTestRule::class,
            'minLengthTest' => MinLengthTestRule::class,
        );
    }

    /**
     * @return array<string, string>
     */
    protected static function defineRuleAliases()
    {
        return array(
            'trimAlias' => 'trimTest',
            'minAlias' => 'minLengthTest',
        );
    }

    /**
     * @return array<string, string>
     */
    protected static function defineRuleMessages()
    {
        return array(
            'minLengthTest' => '$paramName 长度太短',
        );
    }
}
