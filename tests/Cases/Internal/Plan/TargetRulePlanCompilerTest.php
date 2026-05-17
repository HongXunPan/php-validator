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
        $this->assertCount(1, $plans[0]->presentValueNormalizationRules(), 'trim 应归入 present value normalization');
        $this->assertCount(1, $plans[1]->presentValueGuardRules(), 'nullableIfTest 应归入 present value guard');
        $this->assertCount(1, $plans[1]->presentValueAssertionRules(), 'string 应归入 present value assertion');
        $this->assertCount(1, $plans[2]->crossFieldAssertionRules(), 'timeAfterField 应归入 cross field assertion');
        $this->assertCount(1, $plans[3]->unsupportedRules(), '不存在规则应在编译期标记为 unsupported');
    }

    public function testCompileCachesParsedRuleArgument()
    {
        $compiler = new TargetRulePlanCompiler(
            new RuleStringParser(),
            RuleSet::fromValidatorClass(ConditionalValidator::class),
            new DeclaredTargetTreeBuilder()
        );

        $compiledPlan = $compiler->compile(array(
            'name:姓名' => 'parsedPair:left,right',
        ));

        $plans = $compiledPlan->targetPlans();
        $rules = $plans[0]->presentValueAssertionRules();

        $this->assertCount(1, $rules, 'parsedPair 应归入 present value assertion');
        $this->assertSame(array('left', 'right'), $rules[0]->parsedArgument(), '编译期应缓存 parser 输出的结构化参数');
    }
}
