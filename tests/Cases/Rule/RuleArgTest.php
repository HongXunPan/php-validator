<?php

namespace HongXunPan\Validator\Tests\Cases\Rule;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Rule\RuleArg;
use HongXunPan\Validator\Tests\TestCase;

class RuleArgTest extends TestCase
{
    public function testJsonBuildsJsonLiteralArgument()
    {
        $this->assertSame(
            '["草稿",1,null]',
            (string)RuleArg::json(array('草稿', 1, null)),
            'json() 应生成 JSON literal 参数'
        );
    }

    public function testFieldBuildsTrimmedFieldReferenceArgument()
    {
        $this->assertSame(
            'task.target_mode',
            (string)RuleArg::field(' task.target_mode '),
            'field() 应复用字段引用参数约束并清理路径'
        );
    }

    public function testFieldValueBuildsFieldExpectedLiteralArgument()
    {
        $this->assertSame(
            'task.target_mode,"activity"',
            (string)RuleArg::fieldValue('task.target_mode', 'activity'),
            'fieldValue() 应生成 field,json(value) 参数'
        );
    }

    public function testFieldValuesBuildsFieldExpectedLiteralSetArgument()
    {
        $this->assertSame(
            'status,[1,"pending",null]',
            (string)RuleArg::fieldValues('status', array(1, 'pending', null)),
            'fieldValues() 应生成 field,json(array) 参数'
        );
    }

    public function testRangeBuildsJsonRangeArgument()
    {
        $this->assertSame('[3,20]', (string)RuleArg::range(3, 20), 'range() 应生成 JSON 范围参数');
    }

    public function testFieldValueRejectsArray()
    {
        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () {
                RuleArg::fieldValue('status', array('pending'));
            },
            '只允许标量或 null'
        );
    }

    public function testFieldValuesRejectsEmptyArray()
    {
        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () {
                RuleArg::fieldValues('status', array());
            },
            '不能为空'
        );
    }

    public function testFieldValuesRejectsNestedArray()
    {
        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () {
                RuleArg::fieldValues('status', array(array('pending')));
            },
            '只允许标量或 null'
        );
    }
}
