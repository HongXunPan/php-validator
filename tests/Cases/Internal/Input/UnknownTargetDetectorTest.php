<?php

namespace HongXunPan\Validator\Tests\Cases\Internal\Input;

use HongXunPan\Validator\Internal\Input\DeclaredTargetTree;
use HongXunPan\Validator\Internal\Input\UnknownTargetDetector;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Tests\TestCase;

class UnknownTargetDetectorTest extends TestCase
{
    public function testCollectDetectsNestedUnknownFields()
    {
        $detector = new UnknownTargetDetector(new PathAccessor());
        $detailItems = $detector->collect(
            array(
                'profile' => array(
                    'name' => 'Alice',
                    'nickname' => 'A',
                ),
                'extra' => 'x',
            ),
            new DeclaredTargetTree(array(
                'profile' => array(
                    'name' => array(
                        '__leaf' => true,
                    ),
                ),
            )),
            'user'
        );

        $this->assertCount(2, $detailItems, '应收集两个未知字段');
        $this->assertSame('user.profile.nickname', $detailItems[0]->param(), '嵌套未知字段路径应正确');
        $this->assertSame('user.extra', $detailItems[1]->param(), '顶层未知字段路径应正确');
    }
}
