<?php

namespace HongXunPan\Validator\Tests\Fixtures\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Rule\Argument\RuleArgumentParserInterface;

class CommaPairArgumentParser implements RuleArgumentParserInterface
{
    /**
     * @param string $rawArgument
     *
     * @return array{0: string, 1: string}
     */
    public function parse($rawArgument)
    {
        return array_pad(explode(',', (string)$rawArgument, 2), 2, '');
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
