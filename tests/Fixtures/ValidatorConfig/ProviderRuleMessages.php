<?php

namespace HongXunPan\Validator\Tests\Fixtures\ValidatorConfig;

class ProviderRuleMessages
{
    /**
     * @return array<string, string>
     */
    public static function all()
    {
        return array(
            'minLengthTest' => '$paramName 长度太短',
        );
    }
}
