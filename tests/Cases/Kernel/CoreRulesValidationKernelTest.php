<?php

namespace HongXunPan\Validator\Tests\Cases\Kernel;

use HongXunPan\Validator\Tests\Fixtures\Validator\CanonicalValidator;
use HongXunPan\Validator\ValidationKernel;
use HongXunPan\Validator\Tests\TestCase;

class CoreRulesValidationKernelTest extends TestCase
{
    public function testCanonicalTrimNonBlankAndMaxLength()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array('name' => '  Alice  '),
            array('name:姓名' => 'trim|nonBlank|maxLength:10')
        );

        $this->assertTrue($result->isPassed(), 'canonical string 规则应通过');
        $this->assertSame('Alice', $result->validatedData()['name'], 'trim 应返回归一化后的字符串');
    }

    public function testDefaultAndNonNegativeIntNormalizeMissingField()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(),
            array('page:页码' => 'default:1|nonNegativeInt')
        );

        $this->assertTrue($result->isPassed(), 'default + nonNegativeInt 应可补默认值');
        $this->assertSame(1, $result->validatedData()['page'], '默认值应继续经过归一化');
    }

    public function testFormatTimeAndFieldCompareRules()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(
                'start_at' => '2026-05-14 10:00:00',
                'end_at' => '2026/05/14 12:00:00',
            ),
            array(
                'start_at:开始时间' => 'time',
                'end_at:结束时间' => 'formatTime:Y-m-d H:i:s|timeAfterOrEqualField:start_at',
            )
        );

        $this->assertTrue($result->isPassed(), 'formatTime 与时间字段比较应通过');
        $this->assertSame('2026-05-14 12:00:00', $result->validatedData()['end_at'], 'formatTime 应完成时间归一化');
    }

    public function testFieldCompareMessageUsesReferencedFieldDisplayName()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'start_at' => '2026-05-14 10:00:00',
                'end_at' => '2026-05-14 09:00:00',
            ),
            array(
                'start_at:开始时间' => 'time',
                'end_at:结束时间' => 'timeAfterOrEqualField:start_at',
            )
        );

        $this->assertFalse($result->isPassed(), '时间字段比较失败时应返回错误');
        $this->assertContains('开始时间', $result->errors()[0], '错误消息应自动使用被比较字段的显示名');
        $this->assertSame('start_at', $result->detail()[0]['rule_value'], 'detail 中应保留原始字段路径参数');
    }

    public function testCanonicalListRulesCanCompose()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array('ids' => array(3, 2, 2)),
            array('ids:ID列表' => 'listOf|distinct|sortAsc|minItems:1|maxItems:3')
        );

        $this->assertTrue($result->isPassed(), 'canonical 列表规则组合应通过');
        $this->assertSame(array(2, 3), $result->validatedData()['ids'], 'listOf + distinct + sortAsc 应完成归一化');
    }
}
