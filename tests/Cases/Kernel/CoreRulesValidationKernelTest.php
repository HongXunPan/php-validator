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

    public function testMissingFieldSkipsPresentValueTransformRule()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(),
            array('name:姓名' => 'trim|maxLength:10')
        );

        $this->assertTrue($result->isPassed(), '未声明 required 时，missing 字段应跳过 trim 等 present-only 规则');
        $this->assertFalse(
            array_key_exists('name', $result->validatedData()),
            'missing 字段被跳过时不应输出 name'
        );
    }

    public function testMissingFieldSkipsPresentNumericTransformRule()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(),
            array('page:页码' => 'nonNegativeInt')
        );

        $this->assertTrue($result->isPassed(), 'missing 字段不应因 nonNegativeInt 这类 present-only transform 规则失败');
        $this->assertFalse(
            array_key_exists('page', $result->validatedData()),
            'missing 字段跳过 transform 时不应创建 page'
        );
    }

    public function testDefaultOnlyCreatesValueForMissingField()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array('page' => null),
            array('page:页码' => 'default:1')
        );

        $this->assertTrue($result->isPassed(), 'present + null 时 default 不应直接失败');
        $this->assertTrue(array_key_exists('page', $result->validatedData()), '已传入的 null 字段应继续保留');
        $this->assertSame(null, $result->validatedData()['page'], 'default 只处理 missing，不应覆盖 null');
    }

    public function testDefaultCreatedValueContinuesThroughPresentValueTransformRules()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(),
            array('name:姓名' => 'default:  Alice  |trim')
        );

        $this->assertTrue($result->isPassed(), 'default 创建的值后续仍应继续经过 trim');
        $this->assertSame('Alice', $result->validatedData()['name'], 'default 创建的字符串应被后续 trim 归一化');
    }

    public function testRequiredStillRunsAfterMaterializationStage()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(),
            array('name:姓名' => 'required|string')
        );

        $this->assertFalse($result->isPassed(), '缺失必填字段时仍应失败');
        $this->assertSame('姓名', $result->detail()[0]['param'], '错误应落在当前字段自身');
        $this->assertSame('required', $result->detail()[0]['rule'], '应由 required 规则负责报错');
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

    public function testDependentCompareSkipsWhenReferencedTargetPrimaryValidationFailed()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'start_at' => 'not-a-time',
                'end_at' => '2026-05-14 09:00:00',
            ),
            array(
                'start_at:开始时间' => 'time',
                'end_at:结束时间' => 'timeAfterOrEqualField:start_at',
            )
        );

        $this->assertFalse($result->isPassed(), '被依赖字段本地校验失败时整体应失败');
        $this->assertCount(1, $result->errors(), '比较规则应跳过，不应级联产生第二条错误');
        $this->assertSame('开始时间', $result->detail()[0]['param'], '错误应只来自被依赖字段自身');
    }

    public function testDependentCompareUsesMaterializedReferencedValue()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(
                'min_value' => ' 2 ',
                'current_value' => '3',
            ),
            array(
                'min_value:最小值' => 'trim|positiveInt',
                'current_value:当前值' => 'positiveInt|gtField:min_value',
            )
        );

        $this->assertTrue($result->isPassed(), '比较规则应读取被依赖字段物化后的值');
        $this->assertSame(2, $result->validatedData()['min_value'], '被依赖字段应先完成本地归一化');
        $this->assertSame(3, $result->validatedData()['current_value'], '比较通过后当前字段应正常输出');
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
