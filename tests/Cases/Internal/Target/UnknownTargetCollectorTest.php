<?php

namespace HongXunPan\Validator\Tests\Cases\Internal\Target;

use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Parsing\RuleStringParser;
use HongXunPan\Validator\Internal\Target\UnknownTargetCollector;
use HongXunPan\Validator\Tests\TestCase;

class UnknownTargetCollectorTest extends TestCase
{
    public function testCollectDetectsNestedUnknownFields()
    {
        $collector = new UnknownTargetCollector(new RuleStringParser(), new PathAccessor());
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
