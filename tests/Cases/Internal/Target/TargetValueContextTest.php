<?php

namespace HongXunPan\Validator\Tests\Cases\Internal\Target;

use HongXunPan\Validator\Internal\Target\TargetValueContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Tests\TestCase;

class TargetValueContextTest extends TestCase
{
    public function testTracksRawAndCurrentValueLifecycle()
    {
        $context = new TargetValueContext(true, '  Alice  ');

        $this->assertTrue($context->rawExists(), '原始存在性应被保留');
        $this->assertSame('  Alice  ', $context->rawValue(), '原始值应被保留');
        $this->assertTrue($context->currentExists(), '初始当前存在性应等于原始存在性');
        $this->assertSame('  Alice  ', $context->currentValue(), '初始当前值应等于原始值');

        $context->applyRuleResult(RuleResult::pass('Alice'));

        $this->assertSame('Alice', $context->currentValue(), '应用规则结果后当前值应更新');
    }

    public function testMaterializedAndOutputValueCanBeCommitted()
    {
        $context = new TargetValueContext(false, null);

        $context->applyRuleResult(RuleResult::pass('guest', true));
        $context->useCurrentAsMaterialized();
        $context->markDependentReadable();
        $context->commitOutputValue(true);

        $this->assertTrue($context->isMaterialized(), '应能标记为已物化');
        $this->assertTrue($context->materializedExists(), '物化存在性应取当前存在性');
        $this->assertSame('guest', $context->materializedValue(), '物化值应取当前值');
        $this->assertTrue($context->isDependentReadable(), '物化完成后应可显式标记为依赖可读');
        $this->assertTrue($context->hasOutputValue(), '应能提交输出值');
        $this->assertSame('guest', $context->outputValue(), '输出值应按当前值提交');
    }

    public function testCanBeMarkedFailed()
    {
        $context = new TargetValueContext(true, 'Alice');

        $this->assertFalse($context->isFailed(), '默认不应处于失败态');

        $context->markFailed();

        $this->assertTrue($context->isFailed(), '应能标记为失败态');
    }

    public function testCanSkipValueValidation()
    {
        $context = new TargetValueContext(true, null);

        $this->assertFalse($context->shouldSkipValueValidation(), '默认不应跳过值校验');

        $context->skipValueValidation();

        $this->assertTrue($context->shouldSkipValueValidation(), '应能标记跳过值校验');
    }
}
