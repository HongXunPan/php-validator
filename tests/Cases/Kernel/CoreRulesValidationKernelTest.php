<?php

namespace HongXunPan\Validator\Tests\Cases\Kernel;

use HongXunPan\Validator\ValidationKernel;
use HongXunPan\Validator\Tests\TestCase;

class CoreRulesValidationKernelTest extends TestCase
{
    public function testCanonicalTrimNonBlankAndMaxLength()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validateAndNormalize(
            array('name' => '  Alice  '),
            array('name:姓名' => 'trim|nonBlank|maxLength:10')
        );

        $this->assertTrue($result->isPassed(), 'canonical string 规则应通过');
        $this->assertSame('Alice', $result->validatedData()['name'], 'trim 应返回归一化后的字符串');
    }

    public function testLegacyTrimmedRequiredStringAndLenMax()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validateAndNormalize(
            array('name' => '  Alice  '),
            array('name:姓名' => 'trimmedRequiredString|lenMax:10')
        );

        $this->assertTrue($result->isPassed(), 'legacy string alias 应通过');
        $this->assertSame('Alice', $result->validatedData()['name'], 'trimmedRequiredString 应同时完成 trim');
    }

    public function testDefaultAndNonNegativeIntNormalizeMissingField()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validateAndNormalize(
            array(),
            array('page:页码' => 'default:1|nonNegativeInt')
        );

        $this->assertTrue($result->isPassed(), 'default + nonNegativeInt 应可补默认值');
        $this->assertSame(1, $result->validatedData()['page'], '默认值应继续经过归一化');
    }

    public function testRequiredIfFailsWhenTriggered()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validateAndNormalize(
            array('has_fee_notice' => '1'),
            array('fee_notice:收款说明' => 'requiredIf:has_fee_notice,1|trimmedRequiredString')
        );

        $this->assertTrue($result->isFailed(), 'requiredIf 命中时缺字段应失败');
        $this->assertSame('requiredIf', $result->detail()[0]['rule'], '失败规则名应正确保留');
    }

    public function testBlankToNullAndNullableIfCanShortCircuit()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validateAndNormalize(
            array(
                'has_fee_notice' => '0',
                'fee_notice' => '   ',
            ),
            array('fee_notice:收款说明' => 'blankToNull|nullableIf:has_fee_notice,0|trimmedRequiredString')
        );

        $this->assertTrue($result->isPassed(), 'nullableIf 命中时应短路后续规则');
        $this->assertSame(null, $result->validatedData()['fee_notice'], 'blankToNull 后的 null 应被保留');
    }

    public function testProhibitedWithRejectsExistingField()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validateAndNormalize(
            array(
                'display_card_no' => 'A001',
                'qr_code' => 'Q001',
            ),
            array('display_card_no:校友卡号' => 'prohibitedWith:qr_code|trimmedRequiredString')
        );

        $this->assertTrue($result->isFailed(), 'prohibitedWith 命中时应失败');
        $this->assertSame('prohibitedWith', $result->detail()[0]['rule'], '失败规则名应正确保留');
    }

    public function testLegacyAndCanonicalNumberComparisonRules()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validateAndNormalize(
            array('count' => '5'),
            array('count:数量' => 'eq:5|neq:4|gt:4|egt:5|lt:6|lte:5')
        );

        $this->assertTrue($result->isPassed(), 'legacy 与 canonical 比较规则应兼容通过');
    }

    public function testInAndTimeFormatLegacyRules()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validateAndNormalize(
            array(
                'scene' => 'activity',
                'occurred_on' => '2026-05-14',
            ),
            array(
                'scene:场景' => 'in:["activity","member"]',
                'occurred_on:发生日期' => 'timeFormat:Y-m-d',
            )
        );

        $this->assertTrue($result->isPassed(), 'in 与 timeFormat 旧规则应可用');
    }

    public function testFormatTimeAndFieldCompareRules()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validateAndNormalize(
            array(
                'start_at' => '2026-05-14 10:00:00',
                'end_at' => '2026/05/14 12:00:00',
            ),
            array(
                'start_at:开始时间' => 'timeFormat:Y-m-d H:i:s',
                'end_at:结束时间' => 'formatTime:Y-m-d H:i:s|timeAfterOrEqualField:start_at,开始时间',
            )
        );

        $this->assertTrue($result->isPassed(), 'formatTime 与时间字段比较应通过');
        $this->assertSame('2026-05-14 12:00:00', $result->validatedData()['end_at'], 'formatTime 应完成时间归一化');
    }

    public function testArrayCountMaxAndConditionalArrayRules()
    {
        $kernel = ValidationKernel::create(array(), array());
        $failedResult = $kernel->validateAndNormalize(
            array(
                'scope_mode' => 'include',
                'scope_values' => array(),
            ),
            array(
                'scope_values:范围列表' => 'array|arrayCountMax:3|emptyArrayIf:scope_mode,all|nonEmptyArrayIf:scope_mode,include',
            )
        );

        $this->assertTrue($failedResult->isFailed(), '条件数组规则命中时应失败');
        $this->assertSame('nonEmptyArrayIf', $failedResult->detail()[0]['rule'], '失败规则名应正确');
    }

    public function testLegacyNonNegativeIntListUniqueSortedRule()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validateAndNormalize(
            array('ids' => array(3, '2', 2, 1)),
            array('ids:ID列表' => 'nonNegativeIntListUniqueSorted')
        );

        $this->assertTrue($result->isPassed(), 'legacy 列表规则应通过');
        $this->assertSame(array(1, 2, 3), $result->validatedData()['ids'], 'legacy 列表规则应去重并升序');
    }

    public function testCanonicalListRulesCanCompose()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validateAndNormalize(
            array('ids' => array('3', '2', '2')),
            array('ids:ID列表' => 'listOf:nonNegativeInt|distinct|sortAsc|minItems:1|maxItems:3')
        );

        $this->assertTrue($result->isPassed(), 'canonical 列表规则组合应通过');
        $this->assertSame(array(2, 3), $result->validatedData()['ids'], 'listOf + distinct + sortAsc 应完成归一化');
    }
}
