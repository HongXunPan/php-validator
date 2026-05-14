<?php

namespace HongXunPan\Validator\Tests\Cases\Result;

use HongXunPan\Validator\Result\ValidationResult;
use HongXunPan\Validator\Tests\TestCase;

class ValidationResultTest extends TestCase
{
    public function testFailureResultExportsStableEnvelope()
    {
        $result = ValidationResult::failure(
            array('name is required'),
            array('name' => array('name is required')),
            array('name' => null)
        );

        $array = $result->toArray();

        $this->assertTrue($result->isFailed(), 'failure 结果应标记为失败');
        $this->assertSame(1, $result->count(), 'failure 错误数应正确');
        $this->assertArrayHasKey('validated_data', $array, '对外 envelope 应包含 validated_data');
        $this->assertSame(array('name' => null), $array['validated_data'], 'validated_data 应原样输出');
    }

    public function testSuccessResultExportsStableEnvelope()
    {
        $result = ValidationResult::success(array('name' => 'Alice'));
        $array = $result->toArray();

        $this->assertTrue($result->isPassed(), 'success 结果应标记为通过');
        $this->assertSame(0, $result->count(), 'success 错误数应为 0');
        $this->assertCount(0, $array['errors'], 'success errors 应为空');
        $this->assertSame('Alice', $array['validated_data']['name'], 'validated_data 应保留通过值');
    }
}
