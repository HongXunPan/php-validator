<?php

namespace HongXunPan\Validator\Context;

use HongXunPan\Validator\Support\LiteralValueParser;
use HongXunPan\Validator\Support\PathAccessor;

class RuleContext
{
    /**
     * @var string
     */
    private $fieldPath;
    /**
     * @var string
     */
    private $paramName;
    /**
     * @var bool
     */
    private $fieldExists;
    /**
     * @var mixed
     */
    private $value;
    /**
     * @var mixed
     */
    private $ruleArg;
    /**
     * @var array
     */
    private $rawData;
    /**
     * @var PathAccessor
     */
    private $pathAccessor;
    /**
     * @var LiteralValueParser
     */
    private $literalValueParser;

    public function __construct(
        $fieldPath,
        $paramName,
        $fieldExists,
        $value,
        $ruleArg,
        array $rawData,
        PathAccessor $pathAccessor,
        LiteralValueParser $literalValueParser
    ) {
        $this->fieldPath = (string)$fieldPath;
        $this->paramName = (string)$paramName;
        $this->fieldExists = (bool)$fieldExists;
        $this->value = $value;
        $this->ruleArg = $ruleArg;
        $this->rawData = $rawData;
        $this->pathAccessor = $pathAccessor;
        $this->literalValueParser = $literalValueParser;
    }

    public function fieldPath()
    {
        return $this->fieldPath;
    }

    public function paramName()
    {
        return $this->paramName;
    }

    public function fieldExists()
    {
        return $this->fieldExists;
    }

    public function value()
    {
        return $this->value;
    }

    public function ruleArg()
    {
        return $this->ruleArg;
    }

    public function parseRuleArg()
    {
        return $this->parseLiteral($this->ruleArg);
    }

    public function parseLiteral($raw)
    {
        return $this->literalValueParser->parse($raw);
    }

    public function getFieldValue($fieldPath, $strict)
    {
        return $this->pathAccessor->getValue($this->rawData, $fieldPath, $strict);
    }
}
