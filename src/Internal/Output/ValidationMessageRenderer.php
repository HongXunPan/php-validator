<?php

namespace HongXunPan\Validator\Internal\Output;

class ValidationMessageRenderer
{
    public function render($template, $paramName, $displayRuleValue)
    {
        $message = (string)$template;
        $message = str_replace('$paramName', (string)$paramName, $message);
        $message = str_replace('$rule', (string)$displayRuleValue, $message);

        return $message;
    }
}
