<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;

class NoArgumentParser implements RuleArgumentParserInterface
{
    /**
     * @param string $rawArgument
     *
     * @return null
     */
    public function parse($rawArgument)
    {
        return null;
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        return (string)$rawArgument;
    }
}
