<?php

namespace HongXunPan\Validator\Tests\Cases\Internal\Path;

use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Tests\TestCase;

class PathAccessorTest extends TestCase
{
    public function testGetValueReadsNestedPath()
    {
        $accessor = new PathAccessor();
        $result = $accessor->getValue(array('user' => array('name' => 'Alice')), 'user.name', true);

        $this->assertTrue($result->exists(), '嵌套路径应存在');
        $this->assertSame('Alice', $result->value(), '嵌套路径值应正确返回');
    }

    public function testGetValueHonorsStrictModeForNull()
    {
        $accessor = new PathAccessor();
        $strictResult = $accessor->getValue(array('nickname' => null), 'nickname', true);
        $looseResult = $accessor->getValue(array('nickname' => null), 'nickname', false);

        $this->assertTrue($strictResult->exists(), 'strict 模式下 null 字段仍应视为存在');
        $this->assertFalse($looseResult->exists(), '非 strict 模式下 null 字段应按 isset 语义视为不存在');
    }

    public function testSetValueCreatesNestedArrays()
    {
        $accessor = new PathAccessor();
        $data = array();

        $accessor->setValue($data, 'profile.contact.mobile', '13800138000');

        $this->assertSame('13800138000', $data['profile']['contact']['mobile'], 'setValue 应自动创建中间层级');
    }
}
