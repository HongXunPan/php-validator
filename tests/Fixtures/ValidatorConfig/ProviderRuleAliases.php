<?php

namespace HongXunPan\Validator\Tests\Fixtures\ValidatorConfig;

class ProviderRuleAliases
{
    /**
     * @return array<string, string>
     */
    public static function all()
    {
        return array(
            'trimAlias' => 'trimTest',
            'minAlias' => 'minLengthTest',
        );
    }
}
