<?php

namespace HongXunPan\Validator\Tests\Cases\Support;

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Support\RuleResultNormalizer;
use HongXunPan\Validator\Tests\TestCase;

class RuleResultNormalizerTest extends TestCase
{
    public function testNormalizeBoolResult()
    {
        $normalizer = new RuleResultNormalizer();
        $result = $normalizer->normalize(true, 'Alice');

        $this->assertTrue($result->passed(), 'true 应归一化为通过');
        $this->assertSame('Alice', $result->value(), 'bool 结果应保留当前值');
    }

    public function testNormalizeArrayResultWithBreak()
    {
        $normalizer = new RuleResultNormalizer();
        $result = $normalizer->normalize(array(
            'passed' => true,
            'value' => 'Bob',
            'break' => true,
        ), 'Alice');

        $this->assertTrue($result->passed(), '数组结果应归一化为通过');
        $this->assertSame('Bob', $result->value(), '数组结果应允许覆盖 value');
        $this->assertTrue($result->shouldBreak(), 'break 标记应保留');
    }

    public function testNormalizeReturnsRuleResultAsIs()
    {
        $normalizer = new RuleResultNormalizer();
        $ruleResult = RuleResult::pass('Carol');

        $this->assertSame($ruleResult, $normalizer->normalize($ruleResult, 'Alice'), 'RuleResult 实例应直接返回');
    }
}
