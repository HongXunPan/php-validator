<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class TimeLiteralArgument
{
    /**
     * @var string
     */
    private $literal;

    /**
     * @var int
     */
    private $timestamp;

    /**
     * @param string $literal
     * @param int $timestamp
     */
    public function __construct($literal, $timestamp)
    {
        $literal = trim((string)$literal);
        if ($literal === '') {
            throw new InvalidRuleArgumentException('时间字面量不能为空');
        }

        $this->literal = $literal;
        $this->timestamp = (int)$timestamp;
    }

    /**
     * @return string
     */
    public function literal()
    {
        return $this->literal;
    }

    /**
     * @return int
     */
    public function timestamp()
    {
        return $this->timestamp;
    }
}
