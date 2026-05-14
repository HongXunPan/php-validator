<?php

namespace HongXunPan\Validator\Context;

use HongXunPan\Validator\Definition\RuleDefinition;

/**
 * RuleContext 承接单条规则执行上下文。
 */
class RuleContext
{
    private $definition;
    private $fieldName;
    private $displayName;
    private $fieldExists;
    private $value;
    private $ruleArgument;
    private $rawData;
    private $options;
    private $kernel;
    private $pathReader;

    public function __construct(
        RuleDefinition $definition,
        $fieldName,
        $displayName,
        $fieldExists,
        $value,
        $ruleArgument,
        array $rawData,
        ValidationOptions $options,
        $kernel = null,
        $pathReader = null
    ) {
        $this->definition = $definition;
        $this->fieldName = (string)$fieldName;
        $this->displayName = (string)$displayName;
        $this->fieldExists = (bool)$fieldExists;
        $this->value = $value;
        $this->ruleArgument = $ruleArgument;
        $this->rawData = $rawData;
        $this->options = $options;
        $this->kernel = $kernel;
        $this->pathReader = $pathReader;
    }

    public function definition()
    {
        return $this->definition;
    }

    public function fieldName()
    {
        return $this->fieldName;
    }

    public function displayName()
    {
        return $this->displayName;
    }

    public function exists()
    {
        return $this->fieldExists;
    }

    public function value()
    {
        return $this->value;
    }

    public function ruleArgument()
    {
        return $this->ruleArgument;
    }

    public function rawData()
    {
        return $this->rawData;
    }

    public function options()
    {
        return $this->options;
    }

    public function kernel()
    {
        return $this->kernel;
    }

    public function pathReader()
    {
        return $this->pathReader;
    }

    public function readPath($fieldPath, $strict = null)
    {
        if ($this->pathReader === null || !method_exists($this->pathReader, 'getValue')) {
            return array(
                'exists' => false,
                'value' => null,
            );
        }

        if ($strict === null) {
            $strict = $this->options->strict();
        }

        return $this->pathReader->getValue($this->rawData, $fieldPath, $strict);
    }
}
