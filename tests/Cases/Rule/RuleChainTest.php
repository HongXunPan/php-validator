<?php

namespace HongXunPan\Validator\Tests\Cases\Rule;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Rule\Assert\Numeric\GteFieldRule;
use HongXunPan\Validator\Rule\Condition\NullableIfNotEqRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfEqRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfInRule;
use HongXunPan\Validator\Rule\RuleChain;
use HongXunPan\Validator\Tests\Fixtures\Validator\CanonicalValidator;
use HongXunPan\Validator\Tests\TestCase;
use HongXunPan\Validator\ValidationKernel;

class RuleChainTest extends TestCase
{
    public function testJoinBuildsRuleString()
    {
        $this->assertSame(
            'required|nonNegativeInt',
            (string)RuleChain::join(array('required', 'nonNegativeInt')),
            'join() 应按 | 组合多条规则 token'
        );
    }

    public function testJoinSkipsNullAndEmptyString()
    {
        $this->assertSame(
            'required|int',
            (string)RuleChain::join(array(null, 'required', '', ' int ')),
            'join() 应跳过 null 与空 token，并清理首尾空白'
        );
    }

    public function testWhenAndWhenNotCanBeUsedInJoin()
    {
        $this->assertSame(
            'required|string',
            (string)RuleChain::join(array(
                RuleChain::when(true, 'required'),
                RuleChain::when(false, 'int'),
                RuleChain::whenNot(false, 'string'),
            )),
            'when() / whenNot() 应能让调用方少写条件分支'
        );
    }

    public function testAppendAddsRuleToken()
    {
        $chain = RuleChain::join(array('required'));
        $chain->append('nonNegativeInt');

        $this->assertSame('required|nonNegativeInt', (string)$chain, 'append() 应追加单条规则 token');
    }

    public function testJoinRejectsComposedToken()
    {
        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () {
                RuleChain::join(array('required|int'));
            },
            '不能包含 |'
        );
    }

    public function testJoinRejectsEmptyChain()
    {
        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () {
                RuleChain::join(array(null, ''));
            },
            '不能为空'
        );
    }

    public function testSemanticRuleBuildersBuildCanonicalTokens()
    {
        $this->assertSame(
            'requiredIfEq:status,"pending"',
            RequiredIfEqRule::ofFieldValue('status', 'pending'),
            'ofFieldValue() 应生成字段等值条件规则'
        );

        $this->assertSame(
            'nullableIfNotEq:status,"approved"',
            NullableIfNotEqRule::ofFieldValue('status', 'approved'),
            'ofFieldValue() 应支持 nullable 条件规则'
        );

        $this->assertSame(
            'requiredIfIn:status,["pending","review"]',
            RequiredIfInRule::ofFieldValues('status', array('pending', 'review')),
            'ofFieldValues() 应生成字段集合条件规则'
        );

        $this->assertSame(
            'gteField:start_at',
            GteFieldRule::ofField('start_at'),
            'ofField() 应支持字段比较规则'
        );
    }

    public function testRuleChainCanBeConsumedByValidationKernel()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $result = $kernel->validate(
            array(
                'task' => array(
                    'target_mode' => 'activity_checkin_users',
                    'source_id' => 'A001',
                ),
            ),
            array(
                'task.target_mode:任务模式' => 'string',
                'task.source_id:任务来源对象ID' => RuleChain::join(array(
                    RequiredIfEqRule::ofFieldValue('task.target_mode', 'activity_checkin_users'),
                    NullableIfNotEqRule::ofFieldValue('task.target_mode', 'activity_checkin_users'),
                    'string',
                )),
            )
        );

        $this->assertTrue($result->isPassed(), 'ValidationKernel 应能消费可字符串化的 RuleChain');
    }
}
