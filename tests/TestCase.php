<?php

namespace HongXunPan\Validator\Tests;

abstract class TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function run($methodName)
    {
        $this->setUp();

        try {
            $this->$methodName();
        } finally {
            $this->tearDown();
        }
    }

    protected function fail($message)
    {
        throw new AssertionFailedException($message);
    }

    protected function assertTrue($actual, $message)
    {
        if ($actual !== true) {
            $this->fail($message . '；实际值：' . $this->exportValue($actual));
        }
    }

    protected function assertFalse($actual, $message)
    {
        if ($actual !== false) {
            $this->fail($message . '；实际值：' . $this->exportValue($actual));
        }
    }

    protected function assertSame($expected, $actual, $message)
    {
        if ($expected !== $actual) {
            $this->fail(
                $message
                . '；期望：' . $this->exportValue($expected)
                . '；实际：' . $this->exportValue($actual)
            );
        }
    }

    protected function assertInstanceOf($expectedClass, $actual, $message)
    {
        if (!is_object($actual) || !($actual instanceof $expectedClass)) {
            $this->fail($message . '；实际值：' . $this->exportValue($actual));
        }
    }

    protected function assertArrayHasKey($expectedKey, array $actual, $message)
    {
        if (!array_key_exists($expectedKey, $actual)) {
            $this->fail($message . '；缺少键：' . $expectedKey);
        }
    }

    protected function assertCount($expectedCount, array $actual, $message)
    {
        $actualCount = count($actual);
        if ($actualCount !== (int)$expectedCount) {
            $this->fail($message . '；期望数量：' . $expectedCount . '；实际数量：' . $actualCount);
        }
    }

    protected function assertContains($expectedPart, $actualText, $message)
    {
        $actualText = (string)$actualText;
        if (strpos($actualText, (string)$expectedPart) === false) {
            $this->fail($message . '；实际文本：' . $actualText);
        }
    }

    protected function assertThrows($expectedClass, $callback, $expectedMessagePart)
    {
        try {
            call_user_func($callback);
        } catch (\Exception $exception) {
            if (!is_a($exception, $expectedClass)) {
                $this->fail(
                    '抛出的异常类型不符合预期；期望：'
                    . $expectedClass
                    . '；实际：'
                    . get_class($exception)
                );
            }

            if ($expectedMessagePart !== null && strpos($exception->getMessage(), $expectedMessagePart) === false) {
                $this->fail(
                    '异常消息不符合预期；期望包含：'
                    . $expectedMessagePart
                    . '；实际：'
                    . $exception->getMessage()
                );
            }

            return $exception;
        }

        $this->fail('未抛出预期异常：' . $expectedClass);
    }

    private function exportValue($value)
    {
        if (is_object($value)) {
            return 'object(' . get_class($value) . ')';
        }

        return var_export($value, true);
    }
}
