<?php

namespace HongXunPan\Validator\Rule;

use HongXunPan\Validator\Rule\Assert\Common\AcceptedRule;
use HongXunPan\Validator\Rule\Assert\Common\ConfirmedRule;
use HongXunPan\Validator\Rule\Assert\Common\DeclinedRule;
use HongXunPan\Validator\Rule\Assert\Common\DifferentFieldRule;
use HongXunPan\Validator\Rule\Assert\Common\EqRule;
use HongXunPan\Validator\Rule\Assert\Common\InRule;
use HongXunPan\Validator\Rule\Assert\Common\NeqRule;
use HongXunPan\Validator\Rule\Assert\Common\NotInRule;
use HongXunPan\Validator\Rule\Assert\Common\SameFieldRule;
use HongXunPan\Validator\Rule\Assert\Numeric\GtFieldRule;
use HongXunPan\Validator\Rule\Assert\Numeric\GtRule;
use HongXunPan\Validator\Rule\Assert\Numeric\GteFieldRule;
use HongXunPan\Validator\Rule\Assert\Numeric\GteRule;
use HongXunPan\Validator\Rule\Assert\Numeric\LtFieldRule;
use HongXunPan\Validator\Rule\Assert\Numeric\LtRule;
use HongXunPan\Validator\Rule\Assert\Numeric\LteFieldRule;
use HongXunPan\Validator\Rule\Assert\Numeric\LteRule;
use HongXunPan\Validator\Rule\Assert\Numeric\NumberRule;
use HongXunPan\Validator\Rule\Assert\Numeric\NumericBetweenRule;
use HongXunPan\Validator\Rule\Assert\Numeric\NumericRule;
use HongXunPan\Validator\Rule\Assert\String\AlphaDashRule;
use HongXunPan\Validator\Rule\Assert\String\AlphaNumRule;
use HongXunPan\Validator\Rule\Assert\String\AlphaRule;
use HongXunPan\Validator\Rule\Assert\String\AsciiRule;
use HongXunPan\Validator\Rule\Assert\String\ContainsRule;
use HongXunPan\Validator\Rule\Assert\String\EmailRule;
use HongXunPan\Validator\Rule\Assert\String\JsonRule;
use HongXunPan\Validator\Rule\Assert\String\LengthBetweenRule;
use HongXunPan\Validator\Rule\Assert\String\LowercaseRule;
use HongXunPan\Validator\Rule\Assert\String\EndsWithRule;
use HongXunPan\Validator\Rule\Assert\String\MaxLengthRule;
use HongXunPan\Validator\Rule\Assert\String\MinLengthRule;
use HongXunPan\Validator\Rule\Assert\String\NotRegexRule;
use HongXunPan\Validator\Rule\Assert\String\RegexRule;
use HongXunPan\Validator\Rule\Assert\String\StartsWithRule;
use HongXunPan\Validator\Rule\Assert\String\UppercaseRule;
use HongXunPan\Validator\Rule\Assert\String\UrlRule;
use HongXunPan\Validator\Rule\Assert\String\UuidRule;
use HongXunPan\Validator\Rule\Assert\String\NonBlankRule;
use HongXunPan\Validator\Rule\Assert\Time\TimeAfterFieldRule;
use HongXunPan\Validator\Rule\Assert\Time\TimeAfterOrEqualRule;
use HongXunPan\Validator\Rule\Assert\Time\TimeAfterOrEqualFieldRule;
use HongXunPan\Validator\Rule\Assert\Time\TimeAfterRule;
use HongXunPan\Validator\Rule\Assert\Time\TimeBeforeFieldRule;
use HongXunPan\Validator\Rule\Assert\Time\TimeBeforeOrEqualRule;
use HongXunPan\Validator\Rule\Assert\Time\TimeBeforeOrEqualFieldRule;
use HongXunPan\Validator\Rule\Assert\Time\TimeBeforeRule;
use HongXunPan\Validator\Rule\Collection\DistinctRule;
use HongXunPan\Validator\Rule\Collection\ItemsBetweenRule;
use HongXunPan\Validator\Rule\Collection\MaxItemsRule;
use HongXunPan\Validator\Rule\Collection\MinItemsRule;
use HongXunPan\Validator\Rule\Collection\SortAscRule;
use HongXunPan\Validator\Rule\Condition\NullableIfEqRule;
use HongXunPan\Validator\Rule\Condition\NullableIfInRule;
use HongXunPan\Validator\Rule\Condition\NullableIfNotEqRule;
use HongXunPan\Validator\Rule\Condition\NullableIfNotInRule;
use HongXunPan\Validator\Rule\Condition\ProhibitedIfEqRule;
use HongXunPan\Validator\Rule\Condition\ProhibitedIfInRule;
use HongXunPan\Validator\Rule\Condition\ProhibitedIfMissingRule;
use HongXunPan\Validator\Rule\Condition\ProhibitedIfNotEqRule;
use HongXunPan\Validator\Rule\Condition\ProhibitedIfNotInRule;
use HongXunPan\Validator\Rule\Condition\ProhibitedIfPresentRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfEqRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfInRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfMissingRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfPresentRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfNotEqRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfNotInRule;
use HongXunPan\Validator\Rule\Presence\DefaultRule;
use HongXunPan\Validator\Rule\Presence\RequiredRule;
use HongXunPan\Validator\Rule\Transform\Common\NullableRule;
use HongXunPan\Validator\Rule\Transform\Common\ToBoolRule;
use HongXunPan\Validator\Rule\Transform\Numeric\NegativeIntRule;
use HongXunPan\Validator\Rule\Transform\Numeric\NonNegativeIntRule;
use HongXunPan\Validator\Rule\Transform\Numeric\NonPositiveIntRule;
use HongXunPan\Validator\Rule\Transform\Numeric\PositiveIntRule;
use HongXunPan\Validator\Rule\Transform\String\BlankToNullRule;
use HongXunPan\Validator\Rule\Transform\String\TrimRule;
use HongXunPan\Validator\Rule\Transform\Time\FormatTimeRule;
use HongXunPan\Validator\Rule\Type\ArrayType;
use HongXunPan\Validator\Rule\Type\BooleanType;
use HongXunPan\Validator\Rule\Type\IntType;
use HongXunPan\Validator\Rule\Type\ListOfType;
use HongXunPan\Validator\Rule\Type\StringType;
use HongXunPan\Validator\Rule\Type\TimeType;

/**
 * 仅供 core 规则装配使用，不属于稳定公开扩展契约。
 *
 * @internal
 */
class CoreRules
{
    public static function classes()
    {
        return array(
            RequiredRule::class,
            DefaultRule::class,
            RequiredIfMissingRule::class,
            RequiredIfPresentRule::class,
            RequiredIfEqRule::class,
            RequiredIfInRule::class,
            RequiredIfNotEqRule::class,
            RequiredIfNotInRule::class,
            ProhibitedIfPresentRule::class,
            ProhibitedIfMissingRule::class,
            ProhibitedIfEqRule::class,
            ProhibitedIfInRule::class,
            ProhibitedIfNotEqRule::class,
            ProhibitedIfNotInRule::class,
            NullableRule::class,
            NullableIfEqRule::class,
            NullableIfInRule::class,
            NullableIfNotEqRule::class,
            NullableIfNotInRule::class,
            StringType::class,
            IntType::class,
            BooleanType::class,
            TimeType::class,
            ArrayType::class,
            ListOfType::class,
            TrimRule::class,
            BlankToNullRule::class,
            ToBoolRule::class,
            FormatTimeRule::class,
            PositiveIntRule::class,
            NonNegativeIntRule::class,
            NegativeIntRule::class,
            NonPositiveIntRule::class,
            NonBlankRule::class,
            AsciiRule::class,
            AlphaRule::class,
            AlphaNumRule::class,
            AlphaDashRule::class,
            LowercaseRule::class,
            UppercaseRule::class,
            StartsWithRule::class,
            EndsWithRule::class,
            ContainsRule::class,
            RegexRule::class,
            NotRegexRule::class,
            EmailRule::class,
            UrlRule::class,
            UuidRule::class,
            JsonRule::class,
            LengthBetweenRule::class,
            MinLengthRule::class,
            MaxLengthRule::class,
            AcceptedRule::class,
            DeclinedRule::class,
            SameFieldRule::class,
            DifferentFieldRule::class,
            ConfirmedRule::class,
            EqRule::class,
            NeqRule::class,
            GtRule::class,
            GteRule::class,
            LtRule::class,
            LteRule::class,
            NumericRule::class,
            NumberRule::class,
            NumericBetweenRule::class,
            InRule::class,
            NotInRule::class,
            GtFieldRule::class,
            GteFieldRule::class,
            LtFieldRule::class,
            LteFieldRule::class,
            TimeAfterRule::class,
            TimeAfterOrEqualRule::class,
            TimeBeforeRule::class,
            TimeBeforeOrEqualRule::class,
            TimeAfterFieldRule::class,
            TimeAfterOrEqualFieldRule::class,
            TimeBeforeFieldRule::class,
            TimeBeforeOrEqualFieldRule::class,
            DistinctRule::class,
            SortAscRule::class,
            ItemsBetweenRule::class,
            MinItemsRule::class,
            MaxItemsRule::class,
        );
    }

    public static function map()
    {
        $map = array();

        foreach (static::classes() as $ruleClass) {
            $map[$ruleClass::key()] = $ruleClass;
        }

        return $map;
    }
}
