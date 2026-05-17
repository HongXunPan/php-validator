<?php

namespace HongXunPan\Validator\Rule;

use HongXunPan\Validator\Rule\Assert\Common\EqRule;
use HongXunPan\Validator\Rule\Assert\Common\InRule;
use HongXunPan\Validator\Rule\Assert\Common\NeqRule;
use HongXunPan\Validator\Rule\Assert\Numeric\GtFieldRule;
use HongXunPan\Validator\Rule\Assert\Numeric\GtRule;
use HongXunPan\Validator\Rule\Assert\Numeric\GteFieldRule;
use HongXunPan\Validator\Rule\Assert\Numeric\GteRule;
use HongXunPan\Validator\Rule\Assert\Numeric\LtFieldRule;
use HongXunPan\Validator\Rule\Assert\Numeric\LtRule;
use HongXunPan\Validator\Rule\Assert\Numeric\LteFieldRule;
use HongXunPan\Validator\Rule\Assert\Numeric\LteRule;
use HongXunPan\Validator\Rule\Assert\String\MaxLengthRule;
use HongXunPan\Validator\Rule\Assert\String\MinLengthRule;
use HongXunPan\Validator\Rule\Assert\String\NonBlankRule;
use HongXunPan\Validator\Rule\Assert\Time\TimeAfterFieldRule;
use HongXunPan\Validator\Rule\Assert\Time\TimeAfterOrEqualFieldRule;
use HongXunPan\Validator\Rule\Assert\Time\TimeBeforeFieldRule;
use HongXunPan\Validator\Rule\Assert\Time\TimeBeforeOrEqualFieldRule;
use HongXunPan\Validator\Rule\Collection\DistinctRule;
use HongXunPan\Validator\Rule\Collection\MaxItemsRule;
use HongXunPan\Validator\Rule\Collection\MinItemsRule;
use HongXunPan\Validator\Rule\Collection\SortAscRule;
use HongXunPan\Validator\Rule\Condition\NullableIfEqRule;
use HongXunPan\Validator\Rule\Condition\NullableIfInRule;
use HongXunPan\Validator\Rule\Condition\NullableIfNotEqRule;
use HongXunPan\Validator\Rule\Condition\NullableIfNotInRule;
use HongXunPan\Validator\Rule\Condition\ProhibitedIfEqRule;
use HongXunPan\Validator\Rule\Condition\ProhibitedIfInRule;
use HongXunPan\Validator\Rule\Condition\ProhibitedIfNotEqRule;
use HongXunPan\Validator\Rule\Condition\ProhibitedIfNotInRule;
use HongXunPan\Validator\Rule\Condition\ProhibitedIfPresentRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfEqRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfInRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfMissingRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfNotEqRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfNotInRule;
use HongXunPan\Validator\Rule\Presence\DefaultRule;
use HongXunPan\Validator\Rule\Presence\RequiredRule;
use HongXunPan\Validator\Rule\Transform\Common\NullableRule;
use HongXunPan\Validator\Rule\Transform\Numeric\NonNegativeIntRule;
use HongXunPan\Validator\Rule\Transform\Numeric\PositiveIntRule;
use HongXunPan\Validator\Rule\Transform\String\BlankToNullRule;
use HongXunPan\Validator\Rule\Transform\String\TrimRule;
use HongXunPan\Validator\Rule\Transform\Time\FormatTimeRule;
use HongXunPan\Validator\Rule\Type\ArrayType;
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
            RequiredIfEqRule::class,
            RequiredIfInRule::class,
            RequiredIfNotEqRule::class,
            RequiredIfNotInRule::class,
            ProhibitedIfPresentRule::class,
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
            TimeType::class,
            ArrayType::class,
            ListOfType::class,
            TrimRule::class,
            BlankToNullRule::class,
            FormatTimeRule::class,
            PositiveIntRule::class,
            NonNegativeIntRule::class,
            NonBlankRule::class,
            MinLengthRule::class,
            MaxLengthRule::class,
            EqRule::class,
            NeqRule::class,
            GtRule::class,
            GteRule::class,
            LtRule::class,
            LteRule::class,
            InRule::class,
            GtFieldRule::class,
            GteFieldRule::class,
            LtFieldRule::class,
            LteFieldRule::class,
            TimeAfterFieldRule::class,
            TimeAfterOrEqualFieldRule::class,
            TimeBeforeFieldRule::class,
            TimeBeforeOrEqualFieldRule::class,
            DistinctRule::class,
            SortAscRule::class,
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
