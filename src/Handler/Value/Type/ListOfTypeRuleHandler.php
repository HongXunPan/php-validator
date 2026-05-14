<?php

namespace HongXunPan\Validator\Handler\Value\Type;

use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;

class ListOfTypeRuleHandler implements ValueRuleHandlerInterface
{
    public static function validate($context)
    {
        $value = $context->value();
        if (!is_array($value)) {
            return false;
        }

        $kernel = $context->kernel();
        if ($kernel === null) {
            return false;
        }

        $options = $context->options()->toArray();
        $options['field_prefix'] = $context->displayName();

        $result = $kernel->validateListAndNormalize($value, $context->ruleArgument(), $options);
        if ($result->isFailed()) {
            return false;
        }

        return array(
            'passed' => true,
            'value' => $result->validatedData(),
        );
    }
}
