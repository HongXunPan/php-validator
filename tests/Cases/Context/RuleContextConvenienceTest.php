<?php

namespace HongXunPan\Validator\Tests\Cases\Context;

use HongXunPan\Validator\Context\PathValue;
use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Support\LiteralValueParser;
use HongXunPan\Validator\Tests\Fixtures\Context\StubRuleValueReader;
use HongXunPan\Validator\Tests\TestCase;

class RuleContextConvenienceTest extends TestCase
{
    public function testCurrentAndRawExposePathValueView()
    {
        $context = new RuleContext(
            'name',
            '姓名',
            null,
            null,
            false,
            'raw-name',
            true,
            'Alice',
            new StubRuleValueReader(),
            new LiteralValueParser()
        );

        $this->assertInstanceOf(PathValue::class, $context->current(), 'current() 应返回 PathValue');
        $this->assertTrue($context->current()->exists(), 'current() 应保留当前 exists');
        $this->assertSame('Alice', $context->current()->value(), 'current() 应保留当前 value');

        $this->assertInstanceOf(PathValue::class, $context->raw(), 'raw() 应返回 PathValue');
        $this->assertFalse($context->raw()->exists(), 'raw() 应保留原始 exists');
        $this->assertSame('raw-name', $context->raw()->value(), 'raw() 应保留原始 value');
    }

    public function testMaterializedAndDependentDelegateToReader()
    {
        $context = new RuleContext(
            'name',
            '姓名',
            null,
            null,
            true,
            'raw-name',
            true,
            'Alice',
            new StubRuleValueReader(
                array(),
                array('flag' => 'need'),
                array('profile.age' => 18)
            ),
            new LiteralValueParser()
        );

        $this->assertTrue($context->materialized('flag')->exists(), 'materialized() 应委托读取 materialized 值');
        $this->assertSame('need', $context->materialized('flag')->value(), 'materialized() 应返回 materialized value');

        $this->assertTrue($context->dependent('profile.age')->exists(), 'dependent() 应委托读取 dependent 值');
        $this->assertSame(18, $context->dependent('profile.age')->value(), 'dependent() 应返回 dependent value');
    }
}
