<?php

namespace HongXunPan\Validator\Tests\Cases\Support;

use HongXunPan\Validator\Support\PathAccessor;
use HongXunPan\Validator\Support\RuleParser;
use HongXunPan\Validator\Support\UnknownFieldCollector;
use HongXunPan\Validator\Tests\TestCase;

class UnknownFieldCollectorTest extends TestCase
{
    public function testCollectDetectsNestedUnknownFields()
    {
        $collector = new UnknownFieldCollector(new RuleParser(), new PathAccessor());
        $detail = $collector->collect(
            array(
                'profile' => array(
                    'name' => 'Alice',
                    'nickname' => 'A',
                ),
                'extra' => 'x',
            ),
            array(
                'profile.name:姓名' => 'trim',
            ),
            'user'
        );

        $this->assertCount(2, $detail, '应收集两个未知字段');
        $this->assertSame('user.profile.nickname', $detail[0]['param'], '嵌套未知字段路径应正确');
        $this->assertSame('user.extra', $detail[1]['param'], '顶层未知字段路径应正确');
    }
}
