<?php

namespace HongXunPan\Validator\Tests\Fixtures\ValidatorConfig;

use HongXunPan\Validator\Tests\Fixtures\Rule\MinLengthTestRule;
use HongXunPan\Validator\Tests\Fixtures\Rule\TrimTestRule;

class ProviderExtraRules
{
    /**
     * @return array<string, class-string>
     */
    public static function all()
    {
        return array(
            'trimTest' => TrimTestRule::class,
            'minLengthTest' => MinLengthTestRule::class,
        );
    }
}
