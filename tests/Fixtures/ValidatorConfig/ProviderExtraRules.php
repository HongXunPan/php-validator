<?php

namespace HongXunPan\Validator\Tests\Fixtures\ValidatorConfig;

use HongXunPan\Validator\Tests\Fixtures\Rule\MinLengthTestRule;
use HongXunPan\Validator\Tests\Fixtures\Rule\TrimTestRule;

class ProviderExtraRules
{
    public static function all(): array
    {
        return array(
            'trimTest' => TrimTestRule::class,
            'minLengthTest' => MinLengthTestRule::class,
        );
    }
}
