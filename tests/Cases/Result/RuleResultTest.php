<?php

namespace HongXunPan\Validator\Tests\Cases\Result;

use HongXunPan\Validator\Context\PathValue;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Tests\TestCase;

class RuleResultTest extends TestCase
{
    public function testPassPathUsesPathValuePayload()
    {
        $result = RuleResult::passPath(new PathValue(true, 'Alice'));

        $this->assertTrue($result->passed(), 'passPath 应返回 passed 结果');
        $this->assertSame('Alice', $result->value(), 'passPath 应保留 path value');
        $this->assertSame(true, $result->exists(), 'passPath 应保留 path exists');
    }

    public function testFailPathUsesPathValuePayload()
    {
        $result = RuleResult::failPath(new PathValue(false, null));

        $this->assertTrue($result->failed(), 'failPath 应返回 failed 结果');
        $this->assertSame(null, $result->value(), 'failPath 应保留 path value');
        $this->assertSame(false, $result->exists(), 'failPath 应保留 path exists');
    }

    public function testPassAndBreakPathUsesPathValuePayload()
    {
        $result = RuleResult::passAndBreakPath(new PathValue(true, null));

        $this->assertTrue($result->passed(), 'passAndBreakPath 应返回 passed 结果');
        $this->assertTrue($result->shouldBreak(), 'passAndBreakPath 应保留 break 语义');
        $this->assertSame(true, $result->exists(), 'passAndBreakPath 应保留 path exists');
    }
}
