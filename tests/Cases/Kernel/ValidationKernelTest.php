<?php

namespace HongXunPan\Validator\Tests\Cases\Kernel;

use ArrayObject;
use HongXunPan\Validator\Exception\InvalidValidatedDataTargetException;
use HongXunPan\Validator\Tests\Fixtures\Validator\ConditionalValidator;
use HongXunPan\Validator\Tests\Fixtures\Validator\CustomValidator;
use HongXunPan\Validator\ValidationKernel;
use HongXunPan\Validator\Tests\TestCase;

class ValidationKernelTest extends TestCase
{
    public function testValidateAndNormalizeRunsCustomRulePipeline()
    {
        $kernel = ValidationKernel::create(CustomValidator::class);

        $result = $kernel->validateAndNormalize(
            array('name' => '  Alice  '),
            array('name:姓名' => 'trimTest|minLengthTest:3')
        );

        $this->assertTrue($result->isPassed(), '自定义规则链应通过');
        $this->assertSame('Alice', $result->validatedData()['name'], 'normalize 模式应返回归一化后的值');
    }

    public function testValidateKeepsOriginalValueWhenRuleNormalizes()
    {
        $kernel = ValidationKernel::create(CustomValidator::class);

        $result = $kernel->validate(
            array('name' => '  Alice  '),
            array('name:姓名' => 'trimTest')
        );

        $this->assertTrue($result->isPassed(), 'validate 也应允许规则通过');
        $this->assertSame('  Alice  ', $result->validatedData()['name'], '非 normalize 模式应保留原始值');
    }

    public function testValidateSupportsAliasRules()
    {
        $kernel = ValidationKernel::create(CustomValidator::class);

        $result = $kernel->validateAndNormalize(
            array('name' => '  Alice  '),
            array('name:姓名' => 'trimAlias|minAlias:3')
        );

        $this->assertTrue($result->isPassed(), 'alias 规则链应通过');
        $this->assertSame('Alice', $result->validatedData()['name'], 'alias 应映射到最终规则并完成归一化');
    }

    public function testValidateUsesMessageOverrideByFinalRuleKey()
    {
        $kernel = ValidationKernel::create(CustomValidator::class);

        $result = $kernel->validateAndNormalize(
            array('name' => 'Al'),
            array('name:姓名' => 'minAlias:3')
        );

        $this->assertTrue($result->isFailed(), '自定义最小长度失败时应返回失败');
        $this->assertContains('长度太短', $result->errors()[0], '文案覆盖应按最终规则名命中');
    }

    public function testValidateRejectsUnsupportedRuleOnExistingField()
    {
        $kernel = ValidationKernel::create(CustomValidator::class);
        $result = $kernel->validate(
            array('name' => 'Alice'),
            array('name:姓名' => 'unsupportedRule')
        );

        $this->assertTrue($result->isFailed(), '不支持的规则应失败');
        $this->assertContains('unsupported', $result->errors()[0], '错误消息应标记 unsupported');
    }

    public function testValidateRejectsUnknownFieldsWhenOptionEnabled()
    {
        $kernel = ValidationKernel::create(CustomValidator::class);

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
        $kernel = ValidationKernel::create(CustomValidator::class);

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

    public function testValidateListAndNormalizeRejectsNonArrayItemForObjectRules()
    {
        $kernel = ValidationKernel::create(CustomValidator::class);

        $result = $kernel->validateListAndNormalize(
            array(
                array('name' => 'Alice'),
                'broken',
            ),
            array(
                'name:姓名' => 'trimTest',
            ),
            array(
                'field_prefix' => 'items',
            )
        );

        $this->assertTrue($result->isFailed(), '对象规则列表遇到非数组项时应失败');
        $this->assertSame('items.2', $result->detail()[0]['param'], '非数组项应记录正确位置');
        $this->assertSame('array', $result->detail()[0]['rule'], '应使用统一的 array 规则标记');
        $this->assertSame('list item not array', $result->detail()[0]['reason'], '应使用统一的列表项类型错误原因');
    }

    public function testConditionalNullableRuleUsesMaterializedDependencyAndSkipsLocalValueValidation()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validateAndNormalize(
            array(
                'flag' => ' skip ',
                'note' => null,
            ),
            array(
                'flag:开关' => 'trim',
                'note:备注' => 'nullableIfTest:flag,skip|string',
            )
        );

        $this->assertTrue($result->isPassed(), 'conditional nullable 命中后应跳过本地 string 校验');
        $this->assertSame('skip', $result->validatedData()['flag'], '依赖字段应先完成物化归一化');
        $this->assertArrayHasKey('note', $result->validatedData(), '命中 nullable 条件时仍应保留当前字段输出');
        $this->assertSame(null, $result->validatedData()['note'], '当前字段应输出 null');
    }

    public function testConditionalRequiredRuleUsesMaterializedDependency()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array(
                'flag' => ' need ',
            ),
            array(
                'flag:开关' => 'trim',
                'name:姓名' => 'requiredIfTest:flag,need|string',
            )
        );

        $this->assertTrue($result->isFailed(), 'conditional required 命中后缺失字段应失败');
        $this->assertSame('姓名', $result->detail()[0]['param'], '错误应落在当前字段');
        $this->assertSame('requiredIfTest', $result->detail()[0]['rule'], '应由 conditional required 规则负责报错');
    }

    public function testWriteValidatedDataToWritesIntoArrayAccessTarget()
    {
        $kernel = ValidationKernel::create(CustomValidator::class);
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
        $kernel = ValidationKernel::create(CustomValidator::class);
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
