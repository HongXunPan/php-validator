<?php

namespace HongXunPan\Validator\Definition;

use HongXunPan\Validator\Config\RuleConfigInterface;
use HongXunPan\Validator\Exception\InvalidRuleDefinitionException;
use HongXunPan\Validator\Handler\PresenceRuleHandlerInterface;
use HongXunPan\Validator\Handler\RuleHandlerInterface;
use HongXunPan\Validator\Handler\ValueRuleHandlerInterface;
use HongXunPan\Validator\Message\MessageTemplate;

/**
 * RuleDefinition 只描述规则，不执行规则。
 */
class RuleDefinition
{
    private $name;
    private $phase;
    private $handlerClass;
    private $config;
    private $defaultMessage;

    private function __construct(RulePhase $phase, RuleName $name, $handlerClass)
    {
        $this->phase = $phase;
        $this->name = $name;
        $this->handlerClass = $handlerClass;
        $this->config = null;
        $this->defaultMessage = null;
    }

    public static function presence($name, $handlerClass)
    {
        return new static(RulePhase::presence(), RuleName::of($name), $handlerClass);
    }

    public static function value($name, $handlerClass)
    {
        return new static(RulePhase::value(), RuleName::of($name), $handlerClass);
    }

    public function withConfig(RuleConfigInterface $config)
    {
        $this->config = $config;

        return $this;
    }

    public function defaultMessage(MessageTemplate $defaultMessage)
    {
        $this->defaultMessage = $defaultMessage;

        return $this;
    }

    public function name()
    {
        return $this->name;
    }

    public function phase()
    {
        return $this->phase;
    }

    public function handlerClass()
    {
        return $this->handlerClass;
    }

    public function config()
    {
        return $this->config;
    }

    public function defaultMessageValue()
    {
        return $this->defaultMessage;
    }

    public function assertValid()
    {
        if (!is_string($this->handlerClass) || $this->handlerClass === '') {
            throw new InvalidRuleDefinitionException('RuleDefinition handlerClass 非法：' . $this->name->value());
        }

        if (!is_subclass_of($this->handlerClass, RuleHandlerInterface::class)) {
            throw new InvalidRuleDefinitionException('RuleDefinition handlerClass 未实现 RuleHandlerInterface：' . $this->handlerClass);
        }

        if ($this->phase->isPresence() && !is_subclass_of($this->handlerClass, PresenceRuleHandlerInterface::class)) {
            throw new InvalidRuleDefinitionException('presence 规则未实现 PresenceRuleHandlerInterface：' . $this->handlerClass);
        }

        if ($this->phase->isValue() && !is_subclass_of($this->handlerClass, ValueRuleHandlerInterface::class)) {
            throw new InvalidRuleDefinitionException('value 规则未实现 ValueRuleHandlerInterface：' . $this->handlerClass);
        }

        if ($this->config !== null && !($this->config instanceof RuleConfigInterface)) {
            throw new InvalidRuleDefinitionException('RuleDefinition config 非法：' . $this->name->value());
        }

        if (!($this->defaultMessage instanceof MessageTemplate)) {
            throw new InvalidRuleDefinitionException('RuleDefinition 缺少默认文案：' . $this->name->value());
        }
    }
}
