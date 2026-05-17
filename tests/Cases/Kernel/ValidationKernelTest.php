<?php

namespace HongXunPan\Validator\Tests\Cases\Kernel;

use ArrayObject;
use HongXunPan\Validator\Exception\InvalidValidatedDataTargetException;
use HongXunPan\Validator\Tests\Fixtures\Validator\ConditionalValidator;
use HongXunPan\Validator\Tests\Fixtures\Validator\CustomValidator;
use HongXunPan\Validator\Tests\Fixtures\Validator\MethodConfigValidator;
use HongXunPan\Validator\Tests\Fixtures\Validator\ProviderConfigValidator;
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

    public function testValidateSupportsMethodBasedRuleConfiguration()
    {
        $kernel = ValidationKernel::create(MethodConfigValidator::class);

        $result = $kernel->validateAndNormalize(
            array('name' => '  Al  '),
            array('name:姓名' => 'trimAlias|minAlias:3')
        );

        $this->assertTrue($result->isFailed(), '方法式规则配置也应命中 alias 与 message override');
        $this->assertContains('长度太短', $result->errors()[0], '方法式配置应能覆盖最终规则文案');
    }

    public function testValidateSupportsProviderClassBasedRuleConfiguration()
    {
        $kernel = ValidationKernel::create(ProviderConfigValidator::class);

        $result = $kernel->validateAndNormalize(
            array('name' => '  Al  '),
            array('name:姓名' => 'trimAlias|minAlias:3')
        );

        $this->assertTrue($result->isFailed(), 'provider class 规则配置也应命中 alias 与 message override');
        $this->assertContains('长度太短', $result->errors()[0], 'provider class 配置应能覆盖最终规则文案');
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

    public function testRequiredIfEqUsesStrictLiteralAndMaterializedDependency()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array(
                'flag' => ' need ',
            ),
            array(
                'flag:开关' => 'trim',
                'name:姓名' => 'requiredIfEq:flag,"need"|string',
            )
        );

        $this->assertTrue($result->isFailed(), 'requiredIfEq 命中后缺失字段应失败');
        $this->assertSame('requiredIfEq', $result->detail()[0]['rule'], '应由 requiredIfEq 规则负责报错');
    }

    public function testRequiredIfEqUsesStrictComparison()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array(
                'flag' => '1',
            ),
            array(
                'flag:开关' => 'string',
                'name:姓名' => 'requiredIfEq:flag,1|string',
            )
        );

        $this->assertTrue($result->isPassed(), 'requiredIfEq 应使用严格比较，字符串 1 不等于数字 1');
    }

    public function testRequiredIfInMatchesAnyStrictLiteral()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array(
                'status' => 'pending',
            ),
            array(
                'status:状态' => 'string',
                'reason:原因' => 'requiredIfIn:status,["pending","review"]|string',
            )
        );

        $this->assertTrue($result->isFailed(), 'requiredIfIn 命中集合任一值后缺失字段应失败');
        $this->assertSame('requiredIfIn', $result->detail()[0]['rule'], '应由 requiredIfIn 规则负责报错');
    }

    public function testNullableIfEqSkipsLocalValueValidation()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validateAndNormalize(
            array(
                'flag' => 'skip',
                'note' => null,
            ),
            array(
                'flag:开关' => 'string',
                'note:备注' => 'nullableIfEq:flag,"skip"|string',
            )
        );

        $this->assertTrue($result->isPassed(), 'nullableIfEq 命中 null 后应跳过本地 string 校验');
        $this->assertArrayHasKey('note', $result->validatedData(), 'nullableIfEq 命中后仍应输出当前字段');
        $this->assertSame(null, $result->validatedData()['note'], 'nullableIfEq 命中后应输出 null');
    }

    public function testNullableIfInSkipsLocalValueValidation()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validateAndNormalize(
            array(
                'status' => 'draft',
                'note' => null,
            ),
            array(
                'status:状态' => 'string',
                'note:备注' => 'nullableIfIn:status,["draft","pending"]|string',
            )
        );

        $this->assertTrue($result->isPassed(), 'nullableIfIn 命中集合任一值后应跳过本地 string 校验');
        $this->assertSame(null, $result->validatedData()['note'], 'nullableIfIn 命中后应输出 null');
    }

    public function testRequiredIfEqRejectsBareStringLiteral()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $this->assertThrows(
            \HongXunPan\Validator\Exception\InvalidRuleArgumentException::class,
            function () use ($kernel) {
                $kernel->validate(
                    array('flag' => 'need'),
                    array('name:姓名' => 'requiredIfEq:flag,need|string')
                );
            },
            '合法 JSON literal'
        );
    }

    public function testRequiredIfMissingRequiresCurrentFieldWhenReferencedFieldMissing()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array(),
            array(
                'email:邮箱' => 'string',
                'phone:手机号' => 'requiredIfMissing:email|string',
            )
        );

        $this->assertTrue($result->isFailed(), 'requiredIfMissing 在引用字段缺失时应要求当前字段存在');
        $this->assertSame('requiredIfMissing', $result->detail()[0]['rule'], '应由 requiredIfMissing 规则负责报错');
    }

    public function testRequiredIfMissingPassesWhenReferencedFieldPresent()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array('email' => 'a@example.com'),
            array(
                'email:邮箱' => 'string',
                'phone:手机号' => 'requiredIfMissing:email|string',
            )
        );

        $this->assertTrue($result->isPassed(), 'requiredIfMissing 在引用字段存在时不应要求当前字段存在');
    }

    public function testProhibitedIfPresentRejectsCurrentFieldWhenReferencedFieldPresent()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array(
                'email' => 'a@example.com',
                'phone' => '13800000000',
            ),
            array(
                'email:邮箱' => 'string',
                'phone:手机号' => 'prohibitedIfPresent:email|string',
            )
        );

        $this->assertTrue($result->isFailed(), 'prohibitedIfPresent 在引用字段存在时应禁止当前字段存在');
        $this->assertSame('prohibitedIfPresent', $result->detail()[0]['rule'], '应由 prohibitedIfPresent 规则负责报错');
    }

    public function testProhibitedIfPresentPassesWhenReferencedFieldMissing()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array('phone' => '13800000000'),
            array(
                'email:邮箱' => 'string',
                'phone:手机号' => 'prohibitedIfPresent:email|string',
            )
        );

        $this->assertTrue($result->isPassed(), 'prohibitedIfPresent 在引用字段缺失时不应禁止当前字段存在');
    }

    public function testRequiredIfNotEqRequiresWhenReferencedFieldDiffers()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array('status' => 'rejected'),
            array(
                'status:状态' => 'string',
                'reason:原因' => 'requiredIfNotEq:status,"approved"|string',
            )
        );

        $this->assertTrue($result->isFailed(), 'requiredIfNotEq 在引用字段不等于目标值时应要求当前字段存在');
        $this->assertSame('requiredIfNotEq', $result->detail()[0]['rule'], '应由 requiredIfNotEq 规则负责报错');
    }

    public function testRequiredIfNotInRequiresWhenReferencedFieldOutsideSet()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array('status' => 'rejected'),
            array(
                'status:状态' => 'string',
                'reason:原因' => 'requiredIfNotIn:status,["approved","pending"]|string',
            )
        );

        $this->assertTrue($result->isFailed(), 'requiredIfNotIn 在引用字段不属于集合时应要求当前字段存在');
        $this->assertSame('requiredIfNotIn', $result->detail()[0]['rule'], '应由 requiredIfNotIn 规则负责报错');
    }

    public function testNullableIfNotEqSkipsLocalValueValidation()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validateAndNormalize(
            array('status' => 'rejected', 'note' => null),
            array(
                'status:状态' => 'string',
                'note:备注' => 'nullableIfNotEq:status,"approved"|string',
            )
        );

        $this->assertTrue($result->isPassed(), 'nullableIfNotEq 命中 null 后应跳过本地 string 校验');
        $this->assertSame(null, $result->validatedData()['note'], 'nullableIfNotEq 命中后应输出 null');
    }

    public function testNullableIfNotInSkipsLocalValueValidation()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validateAndNormalize(
            array('status' => 'rejected', 'note' => null),
            array(
                'status:状态' => 'string',
                'note:备注' => 'nullableIfNotIn:status,["approved","pending"]|string',
            )
        );

        $this->assertTrue($result->isPassed(), 'nullableIfNotIn 命中 null 后应跳过本地 string 校验');
        $this->assertSame(null, $result->validatedData()['note'], 'nullableIfNotIn 命中后应输出 null');
    }

    public function testProhibitedIfEqRejectsCurrentFieldWhenReferencedFieldEquals()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array('status' => 'locked', 'reason' => 'manual'),
            array(
                'status:状态' => 'string',
                'reason:原因' => 'prohibitedIfEq:status,"locked"|string',
            )
        );

        $this->assertTrue($result->isFailed(), 'prohibitedIfEq 在引用字段等于目标值时应禁止当前字段存在');
        $this->assertSame('prohibitedIfEq', $result->detail()[0]['rule'], '应由 prohibitedIfEq 规则负责报错');
    }

    public function testProhibitedIfInRejectsCurrentFieldWhenReferencedFieldInSet()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array('status' => 'locked', 'reason' => 'manual'),
            array(
                'status:状态' => 'string',
                'reason:原因' => 'prohibitedIfIn:status,["locked","closed"]|string',
            )
        );

        $this->assertTrue($result->isFailed(), 'prohibitedIfIn 在引用字段属于集合时应禁止当前字段存在');
        $this->assertSame('prohibitedIfIn', $result->detail()[0]['rule'], '应由 prohibitedIfIn 规则负责报错');
    }

    public function testProhibitedIfNotEqRejectsCurrentFieldWhenReferencedFieldDiffers()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array('status' => 'open', 'reason' => 'manual'),
            array(
                'status:状态' => 'string',
                'reason:原因' => 'prohibitedIfNotEq:status,"locked"|string',
            )
        );

        $this->assertTrue($result->isFailed(), 'prohibitedIfNotEq 在引用字段不等于目标值时应禁止当前字段存在');
        $this->assertSame('prohibitedIfNotEq', $result->detail()[0]['rule'], '应由 prohibitedIfNotEq 规则负责报错');
    }

    public function testProhibitedIfNotInRejectsCurrentFieldWhenReferencedFieldOutsideSet()
    {
        $kernel = ValidationKernel::create(ConditionalValidator::class);

        $result = $kernel->validate(
            array('status' => 'open', 'reason' => 'manual'),
            array(
                'status:状态' => 'string',
                'reason:原因' => 'prohibitedIfNotIn:status,["locked","closed"]|string',
            )
        );

        $this->assertTrue($result->isFailed(), 'prohibitedIfNotIn 在引用字段不属于集合时应禁止当前字段存在');
        $this->assertSame('prohibitedIfNotIn', $result->detail()[0]['rule'], '应由 prohibitedIfNotIn 规则负责报错');
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
