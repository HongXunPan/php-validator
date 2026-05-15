<?php

namespace HongXunPan\Validator\Tests\Cases\Internal\Parsing;

use HongXunPan\Validator\Internal\Parsing\RuleStringParser;
use HongXunPan\Validator\Tests\TestCase;

class RuleStringParserTest extends TestCase
{
    public function testParseTargetKeyResolvesDisplayName()
    {
        $parser = new RuleStringParser();
        $result = $parser->parseTargetKey('profile.mobile:手机号');

        $this->assertSame('profile.mobile', $result->fieldPath(), 'field path 应正确解析');
        $this->assertSame('手机号', $result->displayName(), '显示名应正确解析');
    }

    public function testParseRuleItemOnlySplitsFirstColon()
    {
        $parser = new RuleStringParser();
        $result = $parser->parseRuleItem('formatTime:Y-m-d H:i:s');

        $this->assertSame('formatTime', $result->inputRuleKey(), 'rule key 应正确解析');
        $this->assertSame('Y-m-d H:i:s', $result->rawArgument(), 'rule argument 应保留后续冒号');
    }

    public function testHasRuleAndFindRuleArgumentWorkOnParsedItems()
    {
        $parser = new RuleStringParser();
        $items = $parser->parseRuleItems('trim|minLength:2|formatTime:Y-m-d H:i:s');

        $this->assertTrue($parser->hasRule($items, 'minLength'), 'hasRule 应命中已有规则');
        $this->assertSame('2', $parser->findRuleArgument($items, 'minLength'), 'findRuleArgument 应返回规则参数');
    }
}
