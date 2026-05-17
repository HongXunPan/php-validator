<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;

class FormatStringArgumentParser implements RuleArgumentParserInterface
{
    /**
     * @param string $rawArgument
     *
     * @return FormatStringArgument
     */
    public function parse($rawArgument)
    {
        return new FormatStringArgument($rawArgument);
    }

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap)
    {
        return $parsedArgument instanceof FormatStringArgument
            ? $parsedArgument->format()
            : (string)$rawArgument;
    }
}
