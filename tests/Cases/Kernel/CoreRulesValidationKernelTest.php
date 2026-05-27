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

    public function testNumericAndNumberRequireRealNumbers()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $passed = $kernel->validate(
            array(
                'ratio' => 0.75,
                'count' => 3,
            ),
            array(
                'ratio:比例' => 'numeric',
                'count:数量' => 'number',
            )
        );
        $failed = $kernel->validate(
            array(
                'ratio' => '0.75',
                'count' => '3',
            ),
            array(
                'ratio:比例' => 'numeric',
                'count:数量' => 'number',
            )
        );
        $missing = $kernel->validate(
            array(),
            array(
                'ratio:比例' => 'numeric',
                'count:数量' => 'number',
            )
        );

        $this->assertTrue($passed->isPassed(), 'numeric / number 应接受真实 int 或 float');
        $this->assertFalse($failed->isPassed(), 'numeric / number 不应接受 numeric string');
        $this->assertCount(2, $failed->errors(), '两个 numeric string 字段都应产生错误');
        $this->assertTrue($missing->isPassed(), 'missing 字段未声明 required 时应跳过 numeric / number');
    }

    public function testFloatRuleRequiresRealFloat()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $passed = $kernel->validate(
            array(
                'ratio' => 0.75,
            ),
            array(
                'ratio:比例' => 'float',
            )
        );
        $failed = $kernel->validate(
            array(
                'count' => 3,
                'numeric_string' => '0.75',
            ),
            array(
                'count:数量' => 'float',
                'numeric_string:数字字符串' => 'float',
            )
        );
        $missing = $kernel->validate(
            array(),
            array(
                'ratio:比例' => 'float',
            )
        );

        $this->assertTrue($passed->isPassed(), 'float 应接受真实 float');
        $this->assertFalse($failed->isPassed(), 'float 不应接受 int 或 numeric string');
        $this->assertCount(2, $failed->errors(), 'int 与 numeric string 都应产生错误');
        $this->assertTrue($missing->isPassed(), 'missing 字段未声明 required 时应跳过 float');
    }

    public function testMultipleOfAndDecimalPlacesCanValidateValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'quantity' => 12,
                'step_ratio' => 1.5,
                'amount' => 12.34,
                'count' => 10,
            ),
            array(
                'quantity:数量' => 'multipleOf:3',
                'step_ratio:比例步长' => 'multipleOf:0.5',
                'amount:金额' => 'decimalPlaces:2',
                'count:计数' => 'decimalPlaces:0',
            )
        );

        $this->assertTrue($result->isPassed(), 'multipleOf / decimalPlaces 应通过合法数值输入');
    }

    public function testMultipleOfAndDecimalPlacesRejectInvalidValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'quantity' => 10,
                'step_ratio' => 1.3,
                'amount' => 12.345,
                'count' => 10.5,
                'numeric_string' => '12.30',
            ),
            array(
                'quantity:数量' => 'multipleOf:3',
                'step_ratio:比例步长' => 'multipleOf:0.5',
                'amount:金额' => 'decimalPlaces:2',
                'count:计数' => 'decimalPlaces:0',
                'numeric_string:数字字符串' => 'multipleOf:0.1',
            )
        );

        $this->assertFalse($result->isPassed(), 'multipleOf / decimalPlaces 应拒绝非法数值输入');
        $this->assertCount(5, $result->errors(), '每个非法字段都应产生错误');
    }

    public function testMultipleOfAndDecimalPlacesSkipMissingFieldByDefault()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(),
            array(
                'quantity:数量' => 'multipleOf:3',
                'amount:金额' => 'decimalPlaces:2',
            )
        );

        $this->assertTrue($result->isPassed(), 'missing 字段未声明 required 时应跳过 multipleOf / decimalPlaces');
    }

    public function testNegativeIntAndNonPositiveIntNormalizeIntegerValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(
                'delta' => '-3',
                'offset' => '0',
            ),
            array(
                'delta:变化量' => 'negativeInt',
                'offset:偏移量' => 'nonPositiveInt',
            )
        );

        $this->assertTrue($result->isPassed(), 'negativeInt / nonPositiveInt 应接受合法整数字符串');
        $this->assertSame(-3, $result->validatedData()['delta'], 'negativeInt 应归一化为 int');
        $this->assertSame(0, $result->validatedData()['offset'], 'nonPositiveInt 应归一化为 int');
    }

    public function testNegativeIntAndNonPositiveIntRejectInvalidValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(
                'delta' => '0',
                'offset' => '1',
            ),
            array(
                'delta:变化量' => 'negativeInt',
                'offset:偏移量' => 'nonPositiveInt',
            )
        );

        $this->assertFalse($result->isPassed(), 'negativeInt / nonPositiveInt 应拒绝边界外值');
        $this->assertCount(2, $result->errors(), '两个非法数字字段都应产生错误');
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

    public function testDateAndDateFormatRulesCanValidateValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'date' => '2026-05-14',
                'slash_date' => '2026/05/14',
                'minute_time' => '2026-05-14 10:30',
            ),
            array(
                'date:日期' => 'date',
                'slash_date:斜杠日期' => 'dateFormat:Y/m/d',
                'minute_time:分钟时间' => 'dateFormat:Y-m-d H:i',
            )
        );

        $this->assertTrue($result->isPassed(), 'date / dateFormat 应通过严格匹配的合法日期输入');
    }

    public function testDateAndDateFormatRulesRejectInvalidValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'date' => '2026-02-30',
                'date_with_time' => '2026-05-14 10:30:00',
                'slash_date' => '2026-05-14',
                'natural' => 'next monday',
            ),
            array(
                'date:日期' => 'date',
                'date_with_time:带时间日期' => 'date',
                'slash_date:斜杠日期' => 'dateFormat:Y/m/d',
                'natural:自然语言' => 'dateFormat:Y-m-d',
            )
        );

        $this->assertFalse($result->isPassed(), 'date / dateFormat 应拒绝非法或不严格匹配的日期输入');
        $this->assertCount(4, $result->errors(), '每个非法日期字段都应产生错误');
    }

    public function testDateAndDateFormatRulesSkipMissingFieldByDefault()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(),
            array(
                'date:日期' => 'date',
                'slash_date:斜杠日期' => 'dateFormat:Y/m/d',
            )
        );

        $this->assertTrue($result->isPassed(), 'missing 字段未声明 required 时应跳过 date / dateFormat');
    }

    public function testTimeLiteralCompareRulesCanValidateValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'publish_at' => '2026-05-14 10:00:01',
                'signup_start_at' => '2026-05-14 10:00:00',
                'close_at' => '2026-05-14 09:59:59',
                'archive_at' => '2026-05-14 10:00:00',
            ),
            array(
                'publish_at:发布时间' => 'timeAfter:2026-05-14 10:00:00',
                'signup_start_at:报名开始时间' => 'timeAfterOrEqual:2026-05-14 10:00:00',
                'close_at:关闭时间' => 'timeBefore:2026-05-14 10:00:00',
                'archive_at:归档时间' => 'timeBeforeOrEqual:2026-05-14 10:00:00',
            )
        );

        $this->assertTrue($result->isPassed(), '固定时间字面量比较应通过合法输入');
    }

    public function testTimeLiteralCompareRulesRejectInvalidValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'publish_at' => '2026-05-14 10:00:00',
                'signup_start_at' => '2026-05-14 09:59:59',
                'close_at' => '2026-05-14 10:00:00',
                'archive_at' => '2026-05-14 10:00:01',
            ),
            array(
                'publish_at:发布时间' => 'timeAfter:2026-05-14 10:00:00',
                'signup_start_at:报名开始时间' => 'timeAfterOrEqual:2026-05-14 10:00:00',
                'close_at:关闭时间' => 'timeBefore:2026-05-14 10:00:00',
                'archive_at:归档时间' => 'timeBeforeOrEqual:2026-05-14 10:00:00',
            )
        );

        $this->assertFalse($result->isPassed(), '固定时间字面量比较应拒绝非法输入');
        $this->assertCount(4, $result->errors(), '每个非法时间字段都应产生错误');
    }

    public function testTimeLiteralCompareRulesSkipMissingFieldByDefault()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(),
            array('publish_at:发布时间' => 'timeAfter:2026-05-14 10:00:00')
        );

        $this->assertTrue($result->isPassed(), 'missing 字段未声明 required 时应跳过固定时间比较规则');
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

    public function testBooleanRuleRequiresActualBoolean()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $passed = $kernel->validate(
            array('enabled' => true),
            array('enabled:是否启用' => 'boolean')
        );
        $failed = $kernel->validate(
            array('enabled' => 'true'),
            array('enabled:是否启用' => 'boolean')
        );
        $missing = $kernel->validate(
            array(),
            array('enabled:是否启用' => 'boolean')
        );

        $this->assertTrue($passed->isPassed(), 'boolean 应接受真实 bool 值');
        $this->assertFalse($failed->isPassed(), 'boolean 不应把字符串 true 当作真实 bool');
        $this->assertTrue($missing->isPassed(), 'missing 字段未声明 required 时应跳过 boolean');
    }

    public function testToBoolNormalizesCommonBooleanLikeValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(
                'enabled' => 'true',
                'disabled' => '0',
                'confirmed' => ' YES ',
                'closed' => 'off',
            ),
            array(
                'enabled:是否启用' => 'toBool|boolean',
                'disabled:是否停用' => 'toBool|boolean',
                'confirmed:是否确认' => 'toBool|boolean',
                'closed:是否关闭' => 'toBool|boolean',
            )
        );

        $this->assertTrue($result->isPassed(), 'toBool 应归一化常见布尔形态');
        $this->assertSame(true, $result->validatedData()['enabled'], 'true 字符串应归一化为 true');
        $this->assertSame(false, $result->validatedData()['disabled'], '0 字符串应归一化为 false');
        $this->assertSame(true, $result->validatedData()['confirmed'], 'YES 字符串应归一化为 true');
        $this->assertSame(false, $result->validatedData()['closed'], 'off 字符串应归一化为 false');
    }

    public function testToBoolRejectsInvalidBooleanLikeValue()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array('enabled' => 'maybe'),
            array('enabled:是否启用' => 'toBool')
        );

        $this->assertFalse($result->isPassed(), 'toBool 遇到无法识别的布尔形态应失败');
        $this->assertSame('toBool', $result->detail()[0]['rule'], '失败规则应为 toBool');
    }

    public function testAcceptedAndDeclinedRules()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $passed = $kernel->validate(
            array(
                'terms' => 'on',
                'marketing' => 'no',
            ),
            array(
                'terms:协议' => 'accepted',
                'marketing:营销订阅' => 'declined',
            )
        );
        $failed = $kernel->validate(
            array(
                'terms' => 'off',
                'marketing' => 'yes',
            ),
            array(
                'terms:协议' => 'accepted',
                'marketing:营销订阅' => 'declined',
            )
        );

        $this->assertTrue($passed->isPassed(), 'accepted / declined 应接受常见确认与拒绝值');
        $this->assertFalse($failed->isPassed(), 'accepted / declined 遇到反向值应失败');
        $this->assertCount(2, $failed->errors(), '两个字段都应产生错误');
    }


    public function testStringFormatRulesCanValidateCommonFormats()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'code' => 'ABC-123',
                'blocked' => 'ABC-123',
                'email' => 'alice@example.com',
                'url' => 'https://example.com/path?foo=bar',
                'uuid' => '550e8400-e29b-41d4-a716-446655440000',
                'payload' => '{"name":"Alice"}',
            ),
            array(
                'code:编码' => 'regex:/^[A-Z]+-[0-9]+$/',
                'blocked:屏蔽值' => 'notRegex:/^admin$/',
                'email:邮箱' => 'email',
                'url:链接' => 'url',
                'uuid:UUID' => 'uuid',
                'payload:JSON' => 'json',
            )
        );

        $this->assertTrue($result->isPassed(), '格式规则应通过常见合法输入');
    }

    public function testStringFormatRulesRejectInvalidValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'code' => 'abc',
                'blocked' => 'admin',
                'email' => 'not-email',
                'url' => 'not-url',
                'uuid' => 'not-uuid',
                'payload' => '{bad-json}',
            ),
            array(
                'code:编码' => 'regex:/^[A-Z]+-[0-9]+$/',
                'blocked:屏蔽值' => 'notRegex:/^admin$/',
                'email:邮箱' => 'email',
                'url:链接' => 'url',
                'uuid:UUID' => 'uuid',
                'payload:JSON' => 'json',
            )
        );

        $this->assertFalse($result->isPassed(), '格式规则应拒绝非法输入');
        $this->assertCount(6, $result->errors(), '每个非法字段都应产生错误');
    }

    public function testRegexKeepsColonInsidePatternArgument()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array('time' => '10:30'),
            array('time:时间' => 'regex:/^[0-9]{2}:[0-9]{2}$/')
        );

        $this->assertTrue($result->isPassed(), 'regex 参数应保留第一个冒号之后的内容');
    }

    public function testStringFormatRulesSkipMissingFieldByDefault()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(),
            array(
                'email:邮箱' => 'email',
                'url:链接' => 'url',
                'payload:JSON' => 'json',
            )
        );

        $this->assertTrue($result->isPassed(), 'missing 字段未声明 required 时应跳过格式规则');
    }


    public function testAsciiStringContentRulesCanValidateValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'ascii' => 'hello-123_',
                'alpha' => 'Alice',
                'alpha_num' => 'Alice2026',
                'alpha_dash' => 'Alice_2026-ok',
                'lowercase' => 'alice-2026',
                'uppercase' => 'ALICE-2026',
            ),
            array(
                'ascii:ASCII' => 'ascii',
                'alpha:字母' => 'alpha',
                'alpha_num:字母数字' => 'alphaNum',
                'alpha_dash:字母数字横线' => 'alphaDash',
                'lowercase:小写' => 'lowercase',
                'uppercase:大写' => 'uppercase',
            )
        );

        $this->assertTrue($result->isPassed(), 'ASCII 字符内容规则应通过合法输入');
    }

    public function testAsciiStringContentRulesRejectInvalidValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'ascii' => '你好',
                'alpha' => 'Alice2026',
                'alpha_num' => 'Alice-2026',
                'alpha_dash' => 'Alice.2026',
                'lowercase' => 'Alice-2026',
                'uppercase' => 'ALIce-2026',
            ),
            array(
                'ascii:ASCII' => 'ascii',
                'alpha:字母' => 'alpha',
                'alpha_num:字母数字' => 'alphaNum',
                'alpha_dash:字母数字横线' => 'alphaDash',
                'lowercase:小写' => 'lowercase',
                'uppercase:大写' => 'uppercase',
            )
        );

        $this->assertFalse($result->isPassed(), 'ASCII 字符内容规则应拒绝非法输入');
        $this->assertCount(6, $result->errors(), '每个非法字段都应产生错误');
    }

    public function testAsciiStringContentRulesSkipMissingFieldByDefault()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(),
            array(
                'ascii:ASCII' => 'ascii',
                'alpha:字母' => 'alpha',
                'alpha_num:字母数字' => 'alphaNum',
                'alpha_dash:字母数字横线' => 'alphaDash',
                'lowercase:小写' => 'lowercase',
                'uppercase:大写' => 'uppercase',
            )
        );

        $this->assertTrue($result->isPassed(), 'missing 字段未声明 required 时应跳过 ASCII 字符内容规则');
    }


    public function testStringNeedleRulesCanValidateValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'url' => 'https://example.com/profile/alice',
                'code' => 'event-2026',
                'title' => 'Alumni Event 2026',
            ),
            array(
                'url:链接' => 'startsWith:["http://","https://"]',
                'code:编码' => 'endsWith:"2026"',
                'title:标题' => 'contains:"Event"',
            )
        );

        $this->assertTrue($result->isPassed(), '字符串参数规则应支持 JSON string 与 JSON string array 参数');
    }

    public function testStringNeedleRulesRejectInvalidValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'url' => 'ftp://example.com/profile/alice',
                'code' => 'event-2025',
                'title' => 'Alumni Meetup 2026',
            ),
            array(
                'url:链接' => 'startsWith:["http://","https://"]',
                'code:编码' => 'endsWith:"2026"',
                'title:标题' => 'contains:"Event"',
            )
        );

        $this->assertFalse($result->isPassed(), '字符串参数规则应拒绝不匹配的输入');
        $this->assertCount(3, $result->errors(), '每个不匹配字段都应产生错误');
    }

    public function testStringNeedleRulesSkipMissingFieldByDefault()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(),
            array(
                'url:链接' => 'startsWith:["http://","https://"]',
                'code:编码' => 'endsWith:"2026"',
                'title:标题' => 'contains:"Event"',
            )
        );

        $this->assertTrue($result->isPassed(), 'missing 字段未声明 required 时应跳过字符串参数规则');
    }


    public function testArrayKeyRulesCanValidateValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'profile' => array(
                    'id' => 1,
                    'name' => 'Alice',
                    'status' => 'active',
                ),
                'payload' => array(
                    'title' => 'Event',
                    'visible' => true,
                ),
                'safe' => array(
                    'id' => 1,
                    'name' => 'Alice',
                ),
            ),
            array(
                'profile:资料' => 'requiredKeys:["id","name"]',
                'payload:载荷' => 'prohibitedKeys:["password","token"]',
                'safe:安全字段' => 'arrayKeysIn:["id","name"]',
            )
        );

        $this->assertTrue($result->isPassed(), '数组 key 规则应通过合法输入');
    }

    public function testArrayKeyRulesRejectInvalidValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'profile' => array(
                    'id' => 1,
                ),
                'payload' => array(
                    'title' => 'Event',
                    'token' => 'secret',
                ),
                'safe' => array(
                    'id' => 1,
                    'extra' => true,
                ),
            ),
            array(
                'profile:资料' => 'requiredKeys:["id","name"]',
                'payload:载荷' => 'prohibitedKeys:["password","token"]',
                'safe:安全字段' => 'arrayKeysIn:["id","name"]',
            )
        );

        $this->assertFalse($result->isPassed(), '数组 key 规则应拒绝非法输入');
        $this->assertCount(3, $result->errors(), '每个非法字段都应产生错误');
    }

    public function testArrayKeyRulesSkipMissingFieldByDefault()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(),
            array(
                'profile:资料' => 'requiredKeys:["id","name"]',
                'payload:载荷' => 'prohibitedKeys:["password","token"]',
                'safe:安全字段' => 'arrayKeysIn:["id","name"]',
            )
        );

        $this->assertTrue($result->isPassed(), 'missing 字段未声明 required 时应跳过数组 key 规则');
    }


    public function testSetAndRangeRulesCanValidateValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'status' => 'archived',
                'name' => 'Alice',
                'ids' => array(1, 2, 3),
                'score' => 9.5,
            ),
            array(
                'status:状态' => 'notIn:["draft","disabled"]',
                'name:姓名' => 'lengthBetween:[2,10]',
                'ids:ID列表' => 'itemsBetween:[1,3]',
                'score:分数' => 'numericBetween:[0,10]',
            )
        );

        $this->assertTrue($result->isPassed(), 'notIn 与范围规则应通过合法输入');
    }

    public function testInRuleUsesStrictLiteralSet()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $passed = $kernel->validate(
            array('status' => 'draft', 'level' => 1),
            array(
                'status:状态' => 'in:["draft","published"]',
                'level:等级' => 'in:[1,2]',
            )
        );
        $failed = $kernel->validate(
            array('status' => 'archived', 'level' => '1'),
            array(
                'status:状态' => 'in:["draft","published"]',
                'level:等级' => 'in:[1,2]',
            )
        );

        $this->assertTrue($passed->isPassed(), 'in 应接受严格 literal 集合内的值');
        $this->assertFalse($failed->isPassed(), 'in 应拒绝集合外值，并区分字符串数字与整数');
        $this->assertCount(2, $failed->errors(), '集合外值与类型不一致都应产生错误');
    }

    public function testSetAndRangeRulesRejectInvalidValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'status' => 'draft',
                'name' => 'A',
                'ids' => array(1, 2, 3, 4),
                'score' => 11,
            ),
            array(
                'status:状态' => 'notIn:["draft","disabled"]',
                'name:姓名' => 'lengthBetween:[2,10]',
                'ids:ID列表' => 'itemsBetween:[1,3]',
                'score:分数' => 'numericBetween:[0,10]',
            )
        );

        $this->assertFalse($result->isPassed(), 'notIn 与范围规则应拒绝非法输入');
        $this->assertCount(4, $result->errors(), '每个非法字段都应产生错误');
    }

    public function testSetAndRangeRulesSkipMissingFieldByDefault()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(),
            array(
                'status:状态' => 'notIn:["draft"]',
                'name:姓名' => 'lengthBetween:[2,10]',
                'ids:ID列表' => 'itemsBetween:[1,3]',
                'score:分数' => 'numericBetween:[0,10]',
            )
        );

        $this->assertTrue($result->isPassed(), 'missing 字段未声明 required 时应跳过集合与范围规则');
    }


    public function testSameDifferentAndConfirmedRulesUsePreparedDependentValue()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(
                'email' => ' alice@example.com ',
                'email_confirmation' => 'alice@example.com',
                'old_password' => 'secret-old',
                'new_password' => 'secret-new',
                'nickname' => ' Alice ',
                'display_name' => 'Alice',
            ),
            array(
                'email:邮箱' => 'trim|confirmed',
                'email_confirmation:确认邮箱' => 'trim',
                'old_password:旧密码' => 'differentField:new_password',
                'new_password:新密码' => 'string',
                'nickname:昵称' => 'trim|sameField:display_name',
                'display_name:展示名' => 'trim',
            )
        );

        $this->assertTrue($result->isPassed(), 'sameField / differentField / confirmed 应读取 prepared dependent value');
        $this->assertSame('alice@example.com', $result->validatedData()['email'], 'confirmed 通过后应保留归一化后的当前值');
    }

    public function testSameDifferentAndConfirmedRulesRejectMismatchedValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'email' => 'alice@example.com',
                'email_confirmation' => 'bob@example.com',
                'old_password' => 'same-secret',
                'new_password' => 'same-secret',
                'nickname' => 'Alice',
                'display_name' => 'Bob',
            ),
            array(
                'email:邮箱' => 'confirmed',
                'email_confirmation:确认邮箱' => 'string',
                'old_password:旧密码' => 'differentField:new_password',
                'new_password:新密码' => 'string',
                'nickname:昵称' => 'sameField:display_name',
                'display_name:展示名' => 'string',
            )
        );

        $this->assertFalse($result->isPassed(), '字段关系规则应拒绝不匹配值');
        $this->assertCount(3, $result->errors(), '三个字段关系错误都应被收集');
    }

    public function testConfirmedSupportsExplicitConfirmationField()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'password' => 'secret',
                'repeat_password' => 'secret',
            ),
            array(
                'password:密码' => 'confirmed:repeat_password',
                'repeat_password:重复密码' => 'string',
            )
        );

        $this->assertTrue($result->isPassed(), 'confirmed 应支持显式确认字段路径');
    }

    public function testRequiredIfPresentAndProhibitedIfMissingRules()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $passed = $kernel->validate(
            array(
                'profile' => array('name' => 'Alice'),
                'profile_name' => 'Alice',
            ),
            array(
                'profile.name:档案原姓名' => 'string',
                'profile_name:档案姓名' => 'requiredIfPresent:profile.name',
                'guest_note:来宾备注' => 'prohibitedIfMissing:profile.mobile',
            )
        );
        $failed = $kernel->validate(
            array(
                'profile' => array('name' => 'Alice'),
                'guest_note' => 'should-not-exist',
            ),
            array(
                'profile.name:档案原姓名' => 'string',
                'profile_name:档案姓名' => 'requiredIfPresent:profile.name',
                'guest_note:来宾备注' => 'prohibitedIfMissing:profile.mobile',
            )
        );

        $this->assertTrue($passed->isPassed(), 'requiredIfPresent / prohibitedIfMissing 应通过合法组合');
        $this->assertFalse($failed->isPassed(), 'requiredIfPresent / prohibitedIfMissing 应拒绝非法组合');
        $this->assertCount(2, $failed->errors(), '两个条件 presence 错误都应被收集');
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

    public function testWildcardTargetNormalizesScalarListItems()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array('ids' => array('1', '2')),
            array(
                'ids:ID列表' => 'listOf',
                'ids.*:ID' => 'nonNegativeInt',
            )
        );

        $this->assertTrue($result->isPassed(), 'field.* 应支持标量列表子项校验');
        $this->assertSame(array(1, 2), $result->validatedData()['ids'], 'field.* 应把子项归一化写回原列表');
    }

    public function testWildcardTargetNormalizesObjectListField()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(
                'users' => array(
                    array('name' => ' Alice '),
                    array('name' => ' Bob '),
                ),
            ),
            array(
                'users:用户列表' => 'listOf',
                'users.*.name:姓名' => 'required|trim|string',
            )
        );

        $this->assertTrue($result->isPassed(), 'field.*.name 应支持对象列表字段校验');
        $this->assertSame('Alice', $result->validatedData()['users'][0]['name'], '第一个对象字段应完成 trim');
        $this->assertSame('Bob', $result->validatedData()['users'][1]['name'], '第二个对象字段应完成 trim');
    }

    public function testWildcardTargetReportsMissingChildByConcretePath()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(
                'users' => array(
                    array('name' => 'Alice'),
                    array(),
                ),
            ),
            array(
                'users:用户列表' => 'listOf',
                'users.*.name:姓名' => 'required|trim|string',
            )
        );

        $this->assertFalse($result->isPassed(), '对象列表子字段缺失时应失败');
        $this->assertSame('users.1.name', $result->detail()[0]['param'], '通配目标错误应定位到具体路径');
    }

    public function testWildcardTargetSupportsAssociativeMapValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(
                'labels' => array(
                    'zh' => ' 中文 ',
                    'en' => ' English ',
                ),
            ),
            array(
                'labels:多语言文案' => 'array',
                'labels.*:文案' => 'trim|string',
            )
        );

        $this->assertTrue($result->isPassed(), 'field.* 应同时支持关联数组值');
        $this->assertSame('中文', $result->validatedData()['labels']['zh'], '关联数组 zh 值应归一化');
        $this->assertSame('English', $result->validatedData()['labels']['en'], '关联数组 en 值应归一化');
    }

    public function testWildcardTargetWorksWithRejectUnknown()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(
                'users' => array(
                    array('name' => 'Alice', 'age' => 18),
                ),
            ),
            array(
                'users:用户列表' => 'listOf',
                'users.*.name:姓名' => 'string',
            ),
            array('reject_unknown' => true)
        );

        $this->assertFalse($result->isPassed(), 'reject_unknown 应识别通配声明并继续拦截子项未知字段');
        $this->assertSame('users.0.age', $result->detail()[0]['param'], '未知字段应定位到具体 item 路径');
    }

    public function testWildcardTargetOutputDoesNotDependOnRuleOrder()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array('ids' => array('1', '2')),
            array(
                'ids.*:ID' => 'nonNegativeInt',
                'ids:ID列表' => 'listOf',
            )
        );

        $this->assertTrue($result->isPassed(), '父子规则顺序变化时仍应通过');
        $this->assertSame(array(1, 2), $result->validatedData()['ids'], '父级输出不应覆盖子项归一化结果');
    }

    public function testWildcardTargetDoesNotExpandWhenParentMissing()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array(),
            array(
                'users:用户列表' => 'required|listOf',
                'users.*.name:姓名' => 'required|trim|string',
            )
        );

        $this->assertFalse($result->isPassed(), '父字段缺失时应由父级 required 报错');
        $this->assertCount(1, $result->errors(), '父字段缺失时不应额外展开通配子规则');
        $this->assertSame('用户列表', $result->detail()[0]['param'], '错误应停留在父字段显示名');
    }

    public function testWildcardTargetDoesNotExpandWhenParentIsNotArray()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array('users' => 'bad'),
            array(
                'users:用户列表' => 'listOf',
                'users.*.name:姓名' => 'required|trim|string',
            )
        );

        $this->assertFalse($result->isPassed(), '父字段非数组时应由父级类型规则报错');
        $this->assertCount(1, $result->errors(), '父字段非数组时不应额外展开通配子规则');
        $this->assertSame('用户列表', $result->detail()[0]['param'], '错误应定位到父字段显示名');
        $this->assertSame('listOf', $result->detail()[0]['rule'], '应由 listOf 报错');
    }

    public function testWildcardTargetReportsChildPathWhenItemIsNotArray()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array('users' => array('bad')),
            array(
                'users:用户列表' => 'listOf',
                'users.*.name:姓名' => 'required|trim|string',
            )
        );

        $this->assertFalse($result->isPassed(), 'item 非数组时，子字段 required 应按具体子路径失败');
        $this->assertSame('users.0.name', $result->detail()[0]['param'], 'item 非数组时仍应定位到展开后的子字段路径');
        $this->assertSame('required', $result->detail()[0]['rule'], '应由子字段 required 报错');
    }

    public function testWildcardTargetSupportsNestedWildcards()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $passed = $kernel->validateAndNormalize(
            array(
                'groups' => array(
                    array(
                        'users' => array(
                            array('name' => ' Alice '),
                            array('name' => ' Bob '),
                        ),
                    ),
                ),
            ),
            array(
                'groups:分组列表' => 'listOf',
                'groups.*.users:用户列表' => 'listOf',
                'groups.*.users.*.name:姓名' => 'required|trim|string',
            )
        );
        $failed = $kernel->validateAndNormalize(
            array(
                'groups' => array(
                    array(
                        'users' => array(
                            array('name' => 'Alice'),
                            array(),
                        ),
                    ),
                ),
            ),
            array(
                'groups:分组列表' => 'listOf',
                'groups.*.users:用户列表' => 'listOf',
                'groups.*.users.*.name:姓名' => 'required|trim|string',
            )
        );

        $this->assertTrue($passed->isPassed(), '多层通配应支持正常对象列表嵌套');
        $this->assertSame('Alice', $passed->validatedData()['groups'][0]['users'][0]['name'], '多层通配应归一化内层子字段');
        $this->assertFalse($failed->isPassed(), '多层通配内层子字段缺失时应失败');
        $this->assertSame('groups.0.users.1.name', $failed->detail()[0]['param'], '多层通配错误应定位到具体路径');
    }

    public function testWildcardTargetRejectUnknownAllowsDirectWildcardItems()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array('labels' => array('zh' => '中文', 'en' => 'English')),
            array(
                'labels:多语言文案' => 'array',
                'labels.*:文案' => 'string',
            ),
            array('reject_unknown' => true)
        );

        $this->assertTrue($result->isPassed(), 'reject_unknown 下 field.* 应允许 map 动态 key 本身');
    }

    public function testBracketWildcardSyntaxIsNotSupported()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validateAndNormalize(
            array('ids' => array('1', '2')),
            array(
                'ids:ID列表' => 'listOf',
                'ids.[*]' => 'required|nonNegativeInt',
            )
        );

        $this->assertFalse($result->isPassed(), '[*] 当前应被视为普通路径 segment，而不是通配语法');
        $this->assertSame('ids.[*]', $result->detail()[0]['param'], '不支持语法应按普通路径报错');
    }
}
