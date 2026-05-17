<?php

namespace HongXunPan\Validator\Tests\Cases\Support;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;
use HongXunPan\Validator\Support\StrictLiteralParser;
use HongXunPan\Validator\Tests\TestCase;

class StrictLiteralParserTest extends TestCase
{
    public function testParseJsonScalarLiteral()
    {
        $parser = new StrictLiteralParser();

        $this->assertSame('guest', $parser->parse('"guest"'), 'JSON string literal 应被解析为字符串');
        $this->assertSame(1, $parser->parse('1'), 'JSON number literal 应被解析为数字');
        $this->assertSame(true, $parser->parse('true'), 'JSON bool literal 应被解析为 bool');
        $this->assertSame(null, $parser->parse('null'), 'JSON null literal 应被解析为 null');
    }

    public function testParseJsonArrayLiteral()
    {
        $parser = new StrictLiteralParser();

        $this->assertSame(array(1, 'guest'), $parser->parse('[1,"guest"]'), 'JSON array literal 应被解析为数组');
    }

    public function testParseRejectsBareString()
    {
        $parser = new StrictLiteralParser();

        $this->assertThrows(
            InvalidRuleArgumentException::class,
            function () use ($parser) {
                $parser->parse('guest');
            },
            '合法 JSON literal'
        );
    }
}
