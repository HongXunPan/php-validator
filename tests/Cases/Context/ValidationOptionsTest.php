<?php

namespace HongXunPan\Validator\Tests\Cases\Context;

use HongXunPan\Validator\Context\ValidationOptions;
use HongXunPan\Validator\Tests\TestCase;

class ValidationOptionsTest extends TestCase
{
    public function testWithFieldPrefixKeepsOtherOptions()
    {
        $options = ValidationOptions::fromArray(array(
            'strict' => false,
            'reject_unknown' => true,
            'field_prefix' => 'old',
            'scene' => 'list',
        ));

        $childOptions = $options->withFieldPrefix('items.2');

        $this->assertFalse($childOptions->strict(), '派生选项应保留 strict');
        $this->assertTrue($childOptions->rejectUnknown(), '派生选项应保留 reject_unknown');
        $this->assertSame('items.2', $childOptions->fieldPrefix(), '派生选项应替换 field_prefix');
        $this->assertSame('list', $childOptions->get('scene'), '派生选项应保留 extra');
    }

    public function testForScalarListItemBuildsStableOptions()
    {
        $options = ValidationOptions::forScalarListItem();

        $this->assertTrue($options->strict(), '标量列表项校验应强制 strict');
        $this->assertFalse($options->rejectUnknown(), '标量列表项校验不需要 unknown 拒绝');
        $this->assertSame('', $options->fieldPrefix(), '标量列表项校验不应再叠加 field prefix');
    }
}
