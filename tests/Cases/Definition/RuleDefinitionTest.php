<?php

namespace HongXunPan\Validator\Tests\Cases\Definition;

use HongXunPan\Validator\Definition\RuleDefinition;
use HongXunPan\Validator\Exception\InvalidRuleDefinitionException;
use HongXunPan\Validator\Message\MessageTemplate;
use HongXunPan\Validator\Tests\Fixtures\Config\DummyRuleConfig;
use HongXunPan\Validator\Tests\Fixtures\Handler\DummyPresenceHandler;
use HongXunPan\Validator\Tests\Fixtures\Handler\DummyRuleHandler;
use HongXunPan\Validator\Tests\Fixtures\Handler\DummyValueHandler;
use HongXunPan\Validator\Tests\TestCase;

class RuleDefinitionTest extends TestCase
{
    public function testPresenceDefinitionCanPassValidation()
    {
        $definition = RuleDefinition::presence('required', DummyPresenceHandler::class)
            ->defaultMessage(MessageTemplate::of('required'));

        $definition->assertValid();

        $this->assertSame('required', $definition->name()->value(), 'presence 规则名应被保留');
        $this->assertTrue($definition->phase()->isPresence(), 'presence 规则阶段应正确');
        $this->assertSame(DummyPresenceHandler::class, $definition->handlerClass(), 'presence handler 应被保留');
    }

    public function testValueDefinitionAcceptsConfigAndMessage()
    {
        $config = new DummyRuleConfig();
        $definition = RuleDefinition::value('minLength', DummyValueHandler::class)
            ->withConfig($config)
            ->defaultMessage(MessageTemplate::of('minLength'));

        $definition->assertValid();

        $this->assertTrue($definition->phase()->isValue(), 'value 规则阶段应正确');
        $this->assertSame($config, $definition->config(), 'config 应被保留');
    }

    public function testAssertValidRequiresDefaultMessage()
    {
        $definition = RuleDefinition::presence('required', DummyPresenceHandler::class);

        $this->assertThrows(
            InvalidRuleDefinitionException::class,
            function () use ($definition) {
                $definition->assertValid();
            },
            '缺少默认文案'
        );
    }

    public function testAssertValidRejectsWrongHandlerType()
    {
        $definition = RuleDefinition::value('custom', DummyRuleHandler::class)
            ->defaultMessage(MessageTemplate::of('custom'));

        $this->assertThrows(
            InvalidRuleDefinitionException::class,
            function () use ($definition) {
                $definition->assertValid();
            },
            '未实现 ValueRuleHandlerInterface'
        );
    }
}
