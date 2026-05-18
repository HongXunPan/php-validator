<?php

namespace HongXunPan\Validator\Tests\Cases\Kernel;

use HongXunPan\Validator\Tests\Fixtures\Validator\ConditionalBaseExtensionValidator;
use HongXunPan\Validator\Tests\TestCase;

class ConditionalExtensionKernelTest extends TestCase
{
    public function testCustomConditionalFieldPresenceRuleCanReuseReferencedEqPrelude()
    {
        $result = ConditionalBaseExtensionValidator::validate(
            array(
                'flag' => 'need',
            ),
            array(
                'flag:开关' => 'string',
                'name:姓名' => 'requiredIfReferencedEqTest:flag,"need"|string',
            )
        );

        $this->assertTrue($result->isFailed(), '命中 referenced eq 前置条件后，缺失字段应失败');
        $this->assertSame('requiredIfReferencedEqTest', $result->detail()[0]['rule'], '应由自定义 conditional field presence rule 负责报错');
    }

    public function testCustomConditionalGuardRuleCanReuseReferencedPresentPreludeAndBreak()
    {
        $result = ConditionalBaseExtensionValidator::validateAndNormalize(
            array(
                'flag' => 1,
                'name' => null,
            ),
            array(
                'flag:开关' => 'int',
                'name:姓名' => 'nullableIfReferencedPresentTest:flag|minLength:2',
            )
        );

        $this->assertTrue($result->isPassed(), '命中 referenced present 前置条件后，null 值应 passAndBreak');
        $this->assertArrayHasKey('name', $result->validatedData(), '通过后应保留 name 字段');
        $this->assertSame(null, $result->validatedData()['name'], 'break 后应保留 null 结果');
    }

    public function testCustomConditionalGuardRuleDoesNotBreakWhenPreludeNotTriggered()
    {
        $result = ConditionalBaseExtensionValidator::validate(
            array(
                'name' => null,
            ),
            array(
                'flag:开关' => 'string',
                'name:姓名' => 'nullableIfReferencedPresentTest:flag|string',
            )
        );

        $this->assertTrue($result->isFailed(), '前置条件未命中时，不应 break 掉后续 string 断言');
        $this->assertSame('string', $result->detail()[0]['rule'], '后续规则仍应继续执行');
    }
}
