<?php

namespace HongXunPan\Validator\Tests\Cases\Internal\Plan;

use HongXunPan\Validator\Internal\Input\DeclaredTargetTreeBuilder;
use HongXunPan\Validator\Internal\Parsing\RuleStringParser;
use HongXunPan\Validator\Internal\Plan\TargetRulePlanCompiler;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Tests\Fixtures\Validator\ConditionalValidator;
use HongXunPan\Validator\Tests\TestCase;

class TargetRulePlanCompilerTest extends TestCase
{
    public function testCompileGroupsRulesByExecutionStage()
    {
        $compiler = new TargetRulePlanCompiler(
            new RuleStringParser(),
            RuleSet::fromValidatorClass(ConditionalValidator::class),
            new DeclaredTargetTreeBuilder()
        );

        $compiledPlan = $compiler->compile(array(
            'flag:开关' => 'trim',
            'note:备注' => 'nullableIfTest:flag,skip|string',
            'end_at:结束时间' => 'timeAfterField:start_at',
            'broken:错误' => 'missingRule',
        ));

        $plans = $compiledPlan->targetPlans();

        $this->assertCount(4, $plans, '应为每个 target 生成独立计划');
        $this->assertCount(1, $plans[0]->materializationRules(), 'trim 应归入 materialization');
        $this->assertCount(1, $plans[1]->conditionalPresenceRules(), 'nullableIfTest 应归入 conditional presence');
        $this->assertCount(1, $plans[1]->localValueRules(), 'string 应归入 local value');
        $this->assertCount(1, $plans[2]->dependentValueRules(), 'timeAfterField 应归入 dependent value');
        $this->assertCount(1, $plans[3]->unsupportedRules(), '不存在规则应在编译期标记为 unsupported');
    }
}
