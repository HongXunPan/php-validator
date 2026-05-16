<?php

namespace HongXunPan\Validator\Tests\Cases\Internal\Context;

use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Internal\Context\TargetValueContextStore;
use HongXunPan\Validator\Internal\Context\TargetValueReader;
use HongXunPan\Validator\Internal\Input\RawInputSource;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Tests\TestCase;

class TargetValueReaderTest extends TestCase
{
    public function testRawPathValuePrefersDeclaredTargetContext()
    {
        $store = new TargetValueContextStore();
        $context = new TargetValueContext(true, '  Alice  ');
        $store->remember('user.name', $context);

        $reader = new TargetValueReader(
            new RawInputSource(array('user' => array('name' => 'Bob')), new PathAccessor()),
            $store
        );

        $value = $reader->rawPathValue('user.name', true);

        $this->assertTrue($value->exists(), '已声明 target 应优先命中上下文');
        $this->assertSame('  Alice  ', $value->value(), 'raw 值应来自 target 上下文而不是 fallback rawData');
    }

    public function testRawPathValueFallsBackToRawDataForUndeclaredTarget()
    {
        $reader = new TargetValueReader(
            new RawInputSource(array('profile' => array('mobile' => '13800138000')), new PathAccessor()),
            new TargetValueContextStore()
        );

        $value = $reader->rawPathValue('profile.mobile', true);

        $this->assertTrue($value->exists(), '未声明 target 时应回退到 rawData 读取');
        $this->assertSame('13800138000', $value->value(), 'fallback rawData 值应正确');
    }
}
