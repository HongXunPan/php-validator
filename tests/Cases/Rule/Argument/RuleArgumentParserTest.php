<?php

namespace HongXunPan\Validator\Tests\Cases\Rule\Argument;

use HongXunPan\Validator\Context\PathLabelMap;
use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralArgument;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralArgumentParser;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgument;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralSetArgumentParser;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgument;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgumentParser;
use HongXunPan\Validator\Rule\Argument\FormatStringArgument;
use HongXunPan\Validator\Rule\Argument\FormatStringArgumentParser;
use HongXunPan\Validator\Rule\Argument\IntArgument;
use HongXunPan\Validator\Rule\Argument\IntArgumentParser;
use HongXunPan\Validator\Rule\Argument\IntRangeArgument;
use HongXunPan\Validator\Rule\Argument\IntRangeArgumentParser;
use HongXunPan\Validator\Rule\Argument\LiteralSetArgument;
use HongXunPan\Validator\Rule\Argument\LiteralSetArgumentParser;
use HongXunPan\Validator\Rule\Argument\NumericRangeArgument;
use HongXunPan\Validator\Rule\Argument\NumericRangeArgumentParser;
use HongXunPan\Validator\Tests\TestCase;

class RuleArgumentParserTest extends TestCase
{
    public function testFieldReferenceArgumentParser()
    {
        $parser = new FieldReferenceArgumentParser();
        $argument = $parser->parse(' profile.name ');

        $this->assertInstanceOf(FieldReferenceArgument::class, $argument, '字段引用 parser 应返回字段引用参数');
        $this->assertSame('profile.name', $argument->fieldPath(), '字段引用 parser 应清理字段路径');
    }

    public function testFieldReferenceArgumentParserDisplayUsesPathLabelMap()
    {
        $parser = new FieldReferenceArgumentParser();
        $argument = $parser->parse('profile.name');
        $pathLabelMap = new PathLabelMap();
        $pathLabelMap->register('profile.name', '姓名');

        $this->assertSame('姓名', $parser->display($argument, 'profile.name', $pathLabelMap), '字段引用 parser 展示时应使用字段显示名');
    }

    public function testFieldExpectedLiteralArgumentParser()
    {
        $parser = new FieldExpectedLiteralArgumentParser();
        $argument = $parser->parse('status,"pending"');

        $this->assertInstanceOf(FieldExpectedLiteralArgument::class, $argument, '字段等值 parser 应返回字段等值参数');
        $this->assertSame('status', $argument->fieldPath(), '字段等值 parser 应解析字段路径');
        $this->assertSame('pending', $argument->expectedValue(), '字段等值 parser 应严格解析 JSON string literal');
    }

    public function testFieldExpectedLiteralArgumentParserRejectsBareString()
    {
        $parser = new FieldExpectedLiteralArgumentParser();

        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () use ($parser) {
                $parser->parse('status,pending');
            },
            '合法 JSON literal'
        );
    }

    public function testFieldExpectedLiteralArgumentParserRejectsArrayLiteral()
    {
        $parser = new FieldExpectedLiteralArgumentParser();

        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () use ($parser) {
                $parser->parse('status,[1,2]');
            },
            '只允许标量或 null'
        );
    }

    public function testFieldExpectedLiteralSetArgumentParser()
    {
        $parser = new FieldExpectedLiteralSetArgumentParser();
        $argument = $parser->parse('status,[1,"pending",null]');

        $this->assertInstanceOf(FieldExpectedLiteralSetArgument::class, $argument, '字段集合 parser 应返回字段集合参数');
        $this->assertSame('status', $argument->fieldPath(), '字段集合 parser 应解析字段路径');
        $this->assertSame(array(1, 'pending', null), $argument->expectedValues(), '字段集合 parser 应解析 JSON array literal');
    }

    public function testFieldExpectedLiteralSetArgumentParserRejectsScalarLiteral()
    {
        $parser = new FieldExpectedLiteralSetArgumentParser();

        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () use ($parser) {
                $parser->parse('status,1');
            },
            '必须是数组 literal'
        );
    }


    public function testLiteralSetArgumentParser()
    {
        $parser = new LiteralSetArgumentParser();
        $argument = $parser->parse('[1,"active",null]');

        $this->assertInstanceOf(LiteralSetArgument::class, $argument, 'literal set parser 应返回集合参数');
        $this->assertSame(array(1, 'active', null), $argument->values(), 'literal set parser 应解析 JSON array literal');
    }

    public function testLiteralSetArgumentParserRejectsScalar()
    {
        $parser = new LiteralSetArgumentParser();

        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () use ($parser) {
                $parser->parse('1');
            },
            '数组 literal'
        );
    }

    public function testIntRangeArgumentParser()
    {
        $parser = new IntRangeArgumentParser();
        $argument = $parser->parse('[2,5]');

        $this->assertInstanceOf(IntRangeArgument::class, $argument, '整数范围 parser 应返回范围参数');
        $this->assertSame(2, $argument->min(), '整数范围 parser 应解析 min');
        $this->assertSame(5, $argument->max(), '整数范围 parser 应解析 max');
    }

    public function testIntRangeArgumentParserRejectsFloat()
    {
        $parser = new IntRangeArgumentParser();

        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () use ($parser) {
                $parser->parse('[1,2.5]');
            },
            'JSON integer literal'
        );
    }

    public function testNumericRangeArgumentParser()
    {
        $parser = new NumericRangeArgumentParser();
        $argument = $parser->parse('[1,2.5]');

        $this->assertInstanceOf(NumericRangeArgument::class, $argument, '数值范围 parser 应返回范围参数');
        $this->assertSame(1, $argument->min(), '数值范围 parser 应解析 int min');
        $this->assertSame(2.5, $argument->max(), '数值范围 parser 应解析 float max');
    }

    public function testRangeArgumentParserRejectsReversedRange()
    {
        $parser = new NumericRangeArgumentParser();

        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () use ($parser) {
                $parser->parse('[5,2]');
            },
            'min 不能大于 max'
        );
    }

    public function testFormatStringArgumentParser()
    {
        $parser = new FormatStringArgumentParser();
        $argument = $parser->parse('Y-m-d H:i:s');

        $this->assertInstanceOf(FormatStringArgument::class, $argument, '格式字符串 parser 应返回格式字符串参数');
        $this->assertSame('Y-m-d H:i:s', $argument->format(), '格式字符串 parser 应保留原始格式');
    }

    public function testIntArgumentParser()
    {
        $parser = new IntArgumentParser();
        $argument = $parser->parse('3');

        $this->assertInstanceOf(IntArgument::class, $argument, '整数 parser 应返回整数参数');
        $this->assertSame(3, $argument->value(), '整数 parser 应解析 JSON integer literal');
    }

    public function testIntArgumentParserRejectsStringLiteral()
    {
        $parser = new IntArgumentParser();

        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () use ($parser) {
                $parser->parse('"3"');
            },
            'JSON integer literal'
        );
    }

    public function testIntArgumentParserRejectsFloatLiteral()
    {
        $parser = new IntArgumentParser();

        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () use ($parser) {
                $parser->parse('3.5');
            },
            'JSON integer literal'
        );
    }
}
