<?php

namespace HongXunPan\Validator\Tests\Cases\Support;

use HongXunPan\Validator\Support\LiteralValueParser;
use HongXunPan\Validator\Tests\TestCase;

class LiteralValueParserTest extends TestCase
{
    public function testParseJsonArrayLiteral()
    {
        $parser = new LiteralValueParser();
        $result = $parser->parse('[1,2,3]');

        $this->assertSame(array(1, 2, 3), $result, 'JSON 数组字面量应被解析');
    }

    public function testParseJsonScalarLiteral()
    {
        $parser = new LiteralValueParser();

        $this->assertSame(true, $parser->parse('true'), '布尔字面量应被解析');
        $this->assertSame(123, $parser->parse('123'), '数字字面量应被解析');
        $this->assertSame(null, $parser->parse('null'), 'null 字面量应被解析');
    }

    public function testParseFallsBackToRawStringWhenJsonInvalid()
    {
        $parser = new LiteralValueParser();

        $this->assertSame('hello-world', $parser->parse('hello-world'), '非法 JSON 应回退为原字符串');
    }
}
