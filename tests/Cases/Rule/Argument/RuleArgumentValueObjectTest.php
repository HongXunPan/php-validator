<?php

namespace HongXunPan\Validator\Tests\Cases\Rule\Argument;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralArgument;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgument;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgument;
use HongXunPan\Validator\Rule\Argument\FormatStringArgument;
use HongXunPan\Validator\Rule\Argument\IntArgument;
use HongXunPan\Validator\Tests\TestCase;

class RuleArgumentValueObjectTest extends TestCase
{
    public function testFieldReferenceArgumentStoresFieldPath()
    {
        $argument = new FieldReferenceArgument(' profile.name ');

        $this->assertSame('profile.name', $argument->fieldPath(), '字段引用参数应保存字段路径');
    }

    public function testFieldExpectedLiteralArgumentAllowsScalarAndNull()
    {
        $stringArgument = new FieldExpectedLiteralArgument('status', 'pending');
        $nullArgument = new FieldExpectedLiteralArgument('deleted_at', null);

        $this->assertSame('status', $stringArgument->fieldPath(), '字段等值参数应保存字段路径');
        $this->assertSame('pending', $stringArgument->expectedValue(), '字段等值参数应保存 expected value');
        $this->assertSame(null, $nullArgument->expectedValue(), '字段等值参数应允许 null');
    }

    public function testFieldExpectedLiteralArgumentRejectsArray()
    {
        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () {
                new FieldExpectedLiteralArgument('status', array(1, 2));
            },
            '只允许标量或 null'
        );
    }

    public function testFieldExpectedLiteralSetArgumentStoresScalarSet()
    {
        $argument = new FieldExpectedLiteralSetArgument('status', array(1, 'pending', null));

        $this->assertSame('status', $argument->fieldPath(), '字段集合参数应保存字段路径');
        $this->assertSame(array(1, 'pending', null), $argument->expectedValues(), '字段集合参数应保存 expected values');
    }

    public function testFieldExpectedLiteralSetArgumentRejectsNestedArray()
    {
        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () {
                new FieldExpectedLiteralSetArgument('status', array(array(1, 2)));
            },
            '只允许标量或 null'
        );
    }

    public function testFormatStringArgumentStoresFormat()
    {
        $argument = new FormatStringArgument('Y-m-d H:i:s');

        $this->assertSame('Y-m-d H:i:s', $argument->format(), '格式字符串参数应保存原始格式');
    }

    public function testIntArgumentStoresValue()
    {
        $argument = new IntArgument(3);

        $this->assertSame(3, $argument->value(), '整数参数应保存 int 值');
    }
}
