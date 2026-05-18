<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;

class ConfirmedFieldArgumentParser implements RuleArgumentParserInterface
{
    /**
     * @param string $rawArgument
     *
     * @return ConfirmedFieldArgument
     */
    public function parse($rawArgument)
    {
        return new ConfirmedFieldArgument($rawArgument);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        if (!$parsedArgument instanceof ConfirmedFieldArgument || $parsedArgument->fieldPath() === null) {
            return (string)$rawArgument;
        }

        return $pathLabelMap->resolve($parsedArgument->fieldPath(), $parsedArgument->fieldPath());
    }
}
