<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;

class FieldReferenceArgumentParser implements RuleArgumentParserInterface
{
    /**
     * @param string $rawArgument
     *
     * @return FieldReferenceArgument
     */
    public function parse($rawArgument)
    {
        return new FieldReferenceArgument($rawArgument);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        $fieldPath = $parsedArgument instanceof FieldReferenceArgument
            ? $parsedArgument->fieldPath()
            : (string)$rawArgument;

        return $pathLabelMap->resolve($fieldPath, $fieldPath);
    }
}
