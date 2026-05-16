<?php

namespace HongXunPan\Validator\Internal\Rules;

final class RuleMessageTemplates
{
    const DEFAULT_TEMPLATE = '$paramName validate failed';
    const UNKNOWN_TEMPLATE = '$paramName is unknown';
    const UNSUPPORTED_RULE_TEMPLATE = '$paramName rule is unsupported: $rule';

    public static function defaultTemplate()
    {
        return self::DEFAULT_TEMPLATE;
    }

    public static function unknownTemplate()
    {
        return self::UNKNOWN_TEMPLATE;
    }

    public static function unsupportedRuleTemplate()
    {
        return self::UNSUPPORTED_RULE_TEMPLATE;
    }
}
