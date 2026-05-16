<?php

namespace HongXunPan\Validator\Tests\Fixtures\ValidatorConfig;

class ProviderRuleAliases
{
    public static function all(): array
    {
        return array(
            'trimAlias' => 'trimTest',
            'minAlias' => 'minLengthTest',
        );
    }
}
