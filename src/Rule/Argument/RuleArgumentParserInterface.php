<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;

interface RuleArgumentParserInterface
{
    /**
     * @param string $rawArgument
     *
     * @return mixed
     */
    public function parse($rawArgument);

    /**
     * @param mixed $parsedArgument
     * @param string $rawArgument
     *
     * @return string
     */
    public function display($parsedArgument, $rawArgument, PathLabelMap $pathLabelMap);
}
