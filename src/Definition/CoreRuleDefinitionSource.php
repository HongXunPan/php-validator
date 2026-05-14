<?php

namespace HongXunPan\Validator\Definition;

use HongXunPan\Validator\Handler\Presence\ConditionalPresenceRuleHandler;
use HongXunPan\Validator\Handler\Presence\DefaultPresenceRuleHandler;
use HongXunPan\Validator\Handler\Presence\RequiredPresenceRuleHandler;
use HongXunPan\Validator\Handler\Value\Collection\ConditionalArrayRuleHandler;
use HongXunPan\Validator\Handler\Value\Collection\LegacyNonNegativeIntListUniqueSortedRuleHandler;
use HongXunPan\Validator\Handler\Value\Collection\ListOperationRuleHandler;
use HongXunPan\Validator\Handler\Value\Common\NullableRuleHandler;
use HongXunPan\Validator\Handler\Value\Compare\FieldComparisonRuleHandler;
use HongXunPan\Validator\Handler\Value\Compare\LiteralComparisonRuleHandler;
use HongXunPan\Validator\Handler\Value\Numeric\IntegerNormalizeRuleHandler;
use HongXunPan\Validator\Handler\Value\String\NonBlankRuleHandler;
use HongXunPan\Validator\Handler\Value\String\StringLengthRuleHandler;
use HongXunPan\Validator\Handler\Value\String\StringTransformRuleHandler;
use HongXunPan\Validator\Handler\Value\Time\FormatTimeRuleHandler;
use HongXunPan\Validator\Handler\Value\Time\TimeFormatRuleHandler;
use HongXunPan\Validator\Handler\Value\Type\ListOfTypeRuleHandler;
use HongXunPan\Validator\Handler\Value\Type\ScalarTypeRuleHandler;
use HongXunPan\Validator\Message\MessageTemplate;

class CoreRuleDefinitionSource implements RuleDefinitionSourceInterface
{
    public static function resolve($name)
    {
        $name = RuleName::of($name)->value();

        switch ($name) {
            case 'required':
                return static::presence($name, RequiredPresenceRuleHandler::class, '$paramName is required');
            case 'default':
                return static::presence($name, DefaultPresenceRuleHandler::class, '$paramName default failed');
            case 'requiredIf':
            case 'requiredWithout':
            case 'prohibitedWith':
            case 'prohibitedUnless':
                return static::presence($name, ConditionalPresenceRuleHandler::class, static::messageFor($name));

            case 'eq':
            case 'neq':
            case 'gt':
            case 'egt':
            case 'gte':
            case 'lt':
            case 'elt':
            case 'lte':
            case 'in':
                return static::value($name, LiteralComparisonRuleHandler::class, static::messageFor($name));

            case 'notnull':
            case 'nonBlank':
            case 'trimmedRequiredString':
                return static::value($name, NonBlankRuleHandler::class, static::messageFor($name));

            case 'int':
            case 'array':
            case 'string':
            case 'time':
                return static::value($name, ScalarTypeRuleHandler::class, static::messageFor($name));

            case 'timeFormat':
                return static::value($name, TimeFormatRuleHandler::class, static::messageFor($name));

            case 'len':
            case 'lenMin':
            case 'lenMax':
            case 'minLength':
            case 'maxLength':
                return static::value($name, StringLengthRuleHandler::class, static::messageFor($name));

            case 'trim':
            case 'trimmedString':
            case 'blankToNull':
                return static::value($name, StringTransformRuleHandler::class, static::messageFor($name));

            case 'positiveInt':
            case 'nonNegativeInt':
                return static::value($name, IntegerNormalizeRuleHandler::class, static::messageFor($name));

            case 'nullable':
            case 'nullableIf':
                return static::value($name, NullableRuleHandler::class, static::messageFor($name));

            case 'formatTime':
            case 'timeFormatNormalize':
                return static::value($name, FormatTimeRuleHandler::class, static::messageFor($name));

            case 'gtField':
            case 'egtField':
            case 'ltField':
            case 'eltField':
            case 'timeAfterField':
            case 'timeAfterOrEqualField':
            case 'timeBeforeField':
            case 'timeBeforeOrEqualField':
                return static::value($name, FieldComparisonRuleHandler::class, static::messageFor($name));

            case 'listOf':
                return static::value($name, ListOfTypeRuleHandler::class, '$paramName must be array');

            case 'distinct':
            case 'sortAsc':
            case 'minItems':
            case 'maxItems':
            case 'arrayCountMax':
                return static::value($name, ListOperationRuleHandler::class, static::messageFor($name));

            case 'nonNegativeIntListUniqueSorted':
                return static::value($name, LegacyNonNegativeIntListUniqueSortedRuleHandler::class, static::messageFor($name));

            case 'emptyArrayIf':
            case 'nonEmptyArrayIf':
                return static::value($name, ConditionalArrayRuleHandler::class, static::messageFor($name));
        }

        return null;
    }

    private static function presence($name, $handlerClass, $message)
    {
        return RuleDefinition::presence($name, $handlerClass)
            ->defaultMessage(MessageTemplate::of($message));
    }

    private static function value($name, $handlerClass, $message)
    {
        return RuleDefinition::value($name, $handlerClass)
            ->defaultMessage(MessageTemplate::of($message));
    }

    private static function messageFor($name)
    {
        $messages = array(
            'requiredIf' => '$paramName is required',
            'requiredWithout' => '$paramName is required',
            'prohibitedWith' => '$paramName is prohibited',
            'prohibitedUnless' => '$paramName is prohibited',
            'eq' => '$paramName must equal to $rule',
            'neq' => '$paramName must not equal to $rule',
            'gt' => '$paramName must greater than $rule',
            'egt' => '$paramName must greater than or equal to $rule',
            'gte' => '$paramName must greater than or equal to $rule',
            'lt' => '$paramName must less than $rule',
            'elt' => '$paramName must less than or equal to $rule',
            'lte' => '$paramName must less than or equal to $rule',
            'in' => '$paramName must in $rule',
            'notnull' => '$paramName can not be blank',
            'nonBlank' => '$paramName can not be blank',
            'trimmedRequiredString' => '$paramName can not be blank',
            'int' => '$paramName must be int',
            'array' => '$paramName must be array',
            'string' => '$paramName must be string',
            'time' => '$paramName must be time',
            'timeFormat' => '$paramName must be time',
            'len' => '$paramName length must in $rule',
            'lenMin' => '$paramName length must be bigger than or equal to $rule',
            'minLength' => '$paramName length must be bigger than or equal to $rule',
            'lenMax' => '$paramName length must be smaller than or equal to $rule',
            'maxLength' => '$paramName length must be smaller than or equal to $rule',
            'trim' => '$paramName must be string',
            'trimmedString' => '$paramName must be string',
            'blankToNull' => '$paramName must be string',
            'positiveInt' => '$paramName must be positive integer',
            'nonNegativeInt' => '$paramName must be non-negative integer',
            'nullable' => '$paramName nullable',
            'nullableIf' => '$paramName nullable',
            'formatTime' => '$paramName must be time',
            'timeFormatNormalize' => '$paramName must be time',
            'gtField' => '$paramName must greater than $rule',
            'egtField' => '$paramName must greater than or equal to $rule',
            'ltField' => '$paramName must less than $rule',
            'eltField' => '$paramName must less than or equal to $rule',
            'timeAfterField' => '$paramName must be after $rule',
            'timeAfterOrEqualField' => '$paramName must be after or equal to $rule',
            'timeBeforeField' => '$paramName must be before $rule',
            'timeBeforeOrEqualField' => '$paramName must be before or equal to $rule',
            'distinct' => '$paramName items must be distinct',
            'sortAsc' => '$paramName items must be sortable',
            'minItems' => '$paramName must contain at least $rule items',
            'maxItems' => '$paramName must contain at most $rule items',
            'arrayCountMax' => '$paramName array count must be smaller than $rule',
            'nonNegativeIntListUniqueSorted' => '$paramName must be array of non-negative integer',
            'emptyArrayIf' => '$paramName must be empty',
            'nonEmptyArrayIf' => '$paramName can not be empty',
        );

        return array_key_exists($name, $messages)
            ? $messages[$name]
            : '$paramName validate failed';
    }
}
