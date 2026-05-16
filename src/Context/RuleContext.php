<?php

namespace HongXunPan\Validator\Context;

use HongXunPan\Validator\Support\LiteralValueParser;

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
     * @var mixed
     */
    private $ruleArg;
    /**
     * @var bool
     */
    private $rawExists;
    /**
     * @var mixed
     */
    private $rawValue;
    /**
     * @var bool
     */
    private $currentExists;
    /**
     * @var mixed
     */
    private $currentValue;
    /**
     * @var RuleValueReaderInterface
     */
    private $ruleValueReader;
    /**
     * @var LiteralValueParser
     */
    private $literalValueParser;

    public function __construct(
        $fieldPath,
        $paramName,
        $ruleArg,
        $rawExists,
        $rawValue,
        $currentExists,
        $currentValue,
        RuleValueReaderInterface $ruleValueReader,
        LiteralValueParser $literalValueParser
    ) {
        $this->fieldPath = (string)$fieldPath;
        $this->paramName = (string)$paramName;
        $this->ruleArg = $ruleArg;
        $this->rawExists = (bool)$rawExists;
        $this->rawValue = $rawValue;
        $this->currentExists = (bool)$currentExists;
        $this->currentValue = $currentValue;
        $this->ruleValueReader = $ruleValueReader;
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
        return $this->currentExists;
    }

    public function rawExists()
    {
        return $this->rawExists;
    }

    public function value()
    {
        return $this->currentValue;
    }

    public function rawValue()
    {
        return $this->rawValue;
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
        return $this->ruleValueReader->rawPathValue($fieldPath, $strict);
    }

    public function getMaterializedTargetValue($fieldPath)
    {
        return $this->ruleValueReader->materializedPathValue($fieldPath);
    }

    public function getDependentTargetValue($fieldPath)
    {
        return $this->ruleValueReader->dependentPathValue($fieldPath);
    }
}
