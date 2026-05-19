<?php

namespace HongXunPan\Validator\Tests\Cases\Rule;

use HongXunPan\Validator\Rule\Assert\Common\InRule;
use HongXunPan\Validator\Tests\Fixtures\Rule\FormatTimeRule;
use HongXunPan\Validator\Tests\Fixtures\Rule\MissingKeyRule;
use HongXunPan\Validator\Tests\TestCase;
use LogicException;

class AbstractRuleTest extends TestCase
{
    public function testKeyReturnsSubclassConstant()
    {
        $this->assertSame('formatTime', FormatTimeRule::key(), 'key() 应返回子类声明的 KEY');
    }

    public function testOfBuildsRuleTokenWithArgument()
    {
        $this->assertSame(
            'formatTime:Y-m-d H:i:s',
            FormatTimeRule::of('Y-m-d H:i:s'),
            'of() 应按首个冒号协议拼接 DSL keyword'
        );
    }

    public function testOfJsonBuildsRuleTokenWithJsonEncodedArgument()
    {
        $this->assertSame(
            'in:["草稿","published",1,null]',
            InRule::ofJson(array('草稿', 'published', 1, null)),
            'ofJson() 应按 JSON 参数协议拼接 DSL keyword'
        );
    }

    public function testKeyThrowsWhenSubclassDoesNotOverrideKey()
    {
        $this->assertThrows(
            LogicException::class,
            function () {
                MissingKeyRule::key();
            },
            '必须覆盖 KEY'
        );
    }
}
