<?php

namespace HongXunPan\Validator\Tests\Cases\Support;

use HongXunPan\Validator\Support\RuleParser;
use HongXunPan\Validator\Tests\TestCase;

class RuleParserTest extends TestCase
{
    public function testParseFieldRuleKeyResolvesDisplayName()
    {
        $parser = new RuleParser();
        $result = $parser->parseFieldRuleKey('profile.mobile:手机号');

        $this->assertSame('profile.mobile', $result['field'], 'field path 应正确解析');
        $this->assertSame('手机号', $result['display_name'], '显示名应正确解析');
    }

    public function testParseRuleItemOnlySplitsFirstColon()
    {
        $parser = new RuleParser();
        $result = $parser->parseRuleItem('formatTime:Y-m-d H:i:s');

        $this->assertSame('formatTime', $result['key'], 'rule key 应正确解析');
        $this->assertSame('Y-m-d H:i:s', $result['argument'], 'rule argument 应保留后续冒号');
    }

    public function testHasRuleAndFindRuleArgumentWorkOnParsedItems()
    {
        $parser = new RuleParser();
        $items = $parser->parseRuleItems('trim|minLength:2|formatTime:Y-m-d H:i:s');

        $this->assertTrue($parser->hasRule($items, 'minLength'), 'hasRule 应命中已有规则');
        $this->assertSame('2', $parser->findRuleArgument($items, 'minLength'), 'findRuleArgument 应返回规则参数');
    }
}
