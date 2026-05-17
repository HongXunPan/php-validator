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
     * @var mixed
     */
    private $parsedRuleArg;
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

    /**
     * @param string $fieldPath
     * @param string $paramName
     * @param mixed $ruleArg
     * @param mixed $parsedRuleArg
     * @param bool $rawExists
     * @param mixed $rawValue
     * @param bool $currentExists
     * @param mixed $currentValue
     */
    public function __construct(
        $fieldPath,
        $paramName,
        $ruleArg,
        $parsedRuleArg,
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
        $this->parsedRuleArg = $parsedRuleArg;
        $this->rawExists = (bool)$rawExists;
        $this->rawValue = $rawValue;
        $this->currentExists = (bool)$currentExists;
        $this->currentValue = $currentValue;
        $this->ruleValueReader = $ruleValueReader;
        $this->literalValueParser = $literalValueParser;
    }

    /**
     * @return string
     */
    public function fieldPath()
    {
        return $this->fieldPath;
    }

    /**
     * @return string
     */
    public function paramName()
    {
        return $this->paramName;
    }

    /**
     * @return bool
     */
    public function fieldExists()
    {
        return $this->currentExists;
    }

    /**
     * @return bool
     */
    public function rawExists()
    {
        return $this->rawExists;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->currentValue;
    }

    /**
     * @return mixed
     */
    public function rawValue()
    {
        return $this->rawValue;
    }

    /**
     * @return mixed
     */
    public function ruleArg()
    {
        return $this->ruleArg;
    }

    /**
     * @return mixed
     */
    public function parsedRuleArg()
    {
        return $this->parsedRuleArg;
    }

    /**
     * @return mixed
     */
    public function parseRuleArg()
    {
        return $this->parseLiteral($this->ruleArg);
    }

    /**
     * @param mixed $raw
     *
     * @return mixed
     */
    public function parseLiteral($raw)
    {
        return $this->literalValueParser->parse($raw);
    }

    /**
     * @param string $fieldPath
     * @param bool $strict
     *
     * @return PathValue
     */
    public function getFieldValue($fieldPath, $strict)
    {
        return $this->ruleValueReader->rawPathValue($fieldPath, $strict);
    }

    /**
     * @param string $fieldPath
     *
     * @return PathValue
     */
    public function getMaterializedTargetValue($fieldPath)
    {
        return $this->ruleValueReader->materializedPathValue($fieldPath);
    }

    /**
     * @param string $fieldPath
     *
     * @return PathValue
     */
    public function getDependentTargetValue($fieldPath)
    {
        return $this->ruleValueReader->dependentPathValue($fieldPath);
    }
}
