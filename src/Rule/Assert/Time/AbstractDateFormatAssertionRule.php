<?php

namespace HongXunPan\Validator\Rule\Assert\Time;

use DateTime;
use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;
use HongXunPan\Validator\Rule\Marker\TimeRule;

abstract class AbstractDateFormatAssertionRule extends AbstractPresentValueAssertionRule implements TimeRule
{
    public static function validate(RuleContext $context)
    {
        $value = $context->value();
        if (!is_string($value)) {
            return RuleResult::fail($value);
        }

        $format = static::format($context->parsedRuleArg());
        if ($format === '') {
            return RuleResult::fail($value);
        }

        return static::matchesDateFormat($value, $format)
            ? RuleResult::pass($value)
            : RuleResult::fail($value);
    }

    /**
     * @param mixed $argument
     *
     * @return string
     */
    protected static function format($argument)
    {
        return 'Y-m-d';
    }

    /**
     * @param string $value
     * @param string $format
     *
     * @return bool
     */
    protected static function matchesDateFormat($value, $format)
    {
        $date = DateTime::createFromFormat('!' . $format, $value);
        $errors = DateTime::getLastErrors();
        $hasErrors = is_array($errors) && ((int)$errors['warning_count'] > 0 || (int)$errors['error_count'] > 0);

        return $date instanceof DateTime
            && !$hasErrors
            && $date->format($format) === $value;
    }
}
