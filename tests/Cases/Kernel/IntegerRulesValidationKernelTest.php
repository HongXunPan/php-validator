<?php

namespace HongXunPan\Validator\Tests\Cases\Kernel;

use HongXunPan\Validator\Tests\Fixtures\Validator\CanonicalValidator;
use HongXunPan\Validator\Tests\TestCase;
use HongXunPan\Validator\ValidationKernel;

class IntegerRulesValidationKernelTest extends TestCase
{
    public function testIntegerRulesRejectBooleanValues()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $rules = array('int', 'positiveInt', 'nonNegativeInt', 'negativeInt', 'nonPositiveInt');

        foreach ($rules as $rule) {
            foreach (array(true, false) as $value) {
                $result = $kernel->validateAndNormalize(array('value' => $value), array('value' => $rule));

                $this->assertFalse($result->isPassed(), $rule . ' 应拒绝 ' . var_export($value, true));
                $this->assertSame(array('value'), $result->failedFieldPaths(), $rule . ' 应保留失败字段路径');
            }
        }
    }

    public function testIntegerRulesPreserveAcceptedValuesAndOutputTypes()
    {
        $kernel = ValidationKernel::create(CanonicalValidator::class);
        $cases = array(
            array('int', 2, 2),
            array('int', '2', '2'),
            array('int', 2.0, 2.0),
            array('positiveInt', '2', 2),
            array('positiveInt', 2.0, 2),
            array('nonNegativeInt', '0', 0),
            array('negativeInt', '-2', -2),
            array('nonPositiveInt', '0', 0),
        );

        foreach ($cases as $case) {
            list($rule, $value, $expected) = $case;
            $result = $kernel->validateAndNormalize(array('value' => $value), array('value' => $rule));

            $this->assertTrue($result->isPassed(), $rule . ' 应继续接受 ' . var_export($value, true));
            $this->assertSame($expected, $result->validatedData()['value'], $rule . ' 的输出值和类型应保持不变');
        }
    }
}
