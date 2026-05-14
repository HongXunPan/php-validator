<?php

namespace HongXunPan\Validator\Tests\Cases\Kernel;

use ArrayObject;
use HongXunPan\Validator\Exception\InvalidValidatedDataTargetException;
use HongXunPan\Validator\ValidationKernel;
use HongXunPan\Validator\Tests\Fixtures\Message\KernelMessageSource;
use HongXunPan\Validator\Tests\Fixtures\Source\KernelRuleDefinitionSource;
use HongXunPan\Validator\Tests\TestCase;

class ValidationKernelTest extends TestCase
{
    public function testValidateAndNormalizeRunsCustomRulePipeline()
    {
        $kernel = ValidationKernel::create(
            array(KernelRuleDefinitionSource::class),
            array(KernelMessageSource::class)
        );

        $result = $kernel->validateAndNormalize(
            array('name' => '  Alice  '),
            array('name:姓名' => 'trimTest|minLengthTest:3')
        );

        $this->assertTrue($result->isPassed(), '自定义规则链应通过');
        $this->assertSame('Alice', $result->validatedData()['name'], 'normalize 模式应返回归一化后的值');
    }

    public function testValidateKeepsOriginalValueWhenRuleNormalizes()
    {
        $kernel = ValidationKernel::create(
            array(KernelRuleDefinitionSource::class),
            array(KernelMessageSource::class)
        );

        $result = $kernel->validate(
            array('name' => '  Alice  '),
            array('name:姓名' => 'trimTest')
        );

        $this->assertTrue($result->isPassed(), 'validate 也应允许规则通过');
        $this->assertSame('  Alice  ', $result->validatedData()['name'], '非 normalize 模式应保留原始值');
    }

    public function testValidateRejectsUnsupportedRuleOnExistingField()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validate(
            array('name' => 'Alice'),
            array('name:姓名' => 'unsupportedRule')
        );

        $this->assertTrue($result->isFailed(), '不支持的规则应失败');
        $this->assertContains('unsupported', $result->errors()[0], '错误消息应标记 unsupported');
    }

    public function testValidateRejectsUnknownFieldsWhenOptionEnabled()
    {
        $kernel = ValidationKernel::create(
            array(KernelRuleDefinitionSource::class),
            array(KernelMessageSource::class)
        );

        $result = $kernel->validateAndNormalize(
            array(
                'profile' => array(
                    'name' => 'Alice',
                    'nickname' => 'A',
                ),
            ),
            array(
                'profile.name:姓名' => 'trimTest',
            ),
            array(
                'reject_unknown' => true,
                'field_prefix' => 'user',
            )
        );

        $this->assertTrue($result->isFailed(), 'reject_unknown 开启时应拦截未知字段');
        $this->assertSame('user.profile.nickname', $result->detail()[0]['param'], '未知字段路径应正确');
    }

    public function testValidateListAndNormalizeSupportsScalarRuleList()
    {
        $kernel = ValidationKernel::create(
            array(KernelRuleDefinitionSource::class),
            array(KernelMessageSource::class)
        );

        $result = $kernel->validateListAndNormalize(
            array('  a  ', '  bb  '),
            'trimTest|minLengthTest:1',
            array(
                'field_prefix' => 'items',
            )
        );

        $this->assertTrue($result->isPassed(), '列表标量规则应通过');
        $this->assertSame(array('a', 'bb'), $result->validatedData(), '列表标量应完成归一化');
    }

    public function testWriteValidatedDataToWritesIntoArrayAccessTarget()
    {
        $kernel = ValidationKernel::create(
            array(KernelRuleDefinitionSource::class),
            array(KernelMessageSource::class)
        );
        $result = $kernel->validateAndNormalize(
            array('name' => '  Alice  '),
            array('name:姓名' => 'trimTest')
        );
        $target = new ArrayObject();

        $kernel->writeValidatedDataTo($result, $target);

        $this->assertSame('Alice', $target['name'], 'validated_data 应写入 ArrayAccess 目标');
    }

    public function testWriteValidatedDataToRejectsInvalidTarget()
    {
        $kernel = ValidationKernel::create(array(), array());
        $result = $kernel->validateAndNormalize(array(), array());

        $this->assertThrows(
            InvalidValidatedDataTargetException::class,
            function () use ($kernel, $result) {
                $kernel->writeValidatedDataTo($result, new \stdClass());
            },
            '必须实现 ArrayAccess'
        );
    }
}
