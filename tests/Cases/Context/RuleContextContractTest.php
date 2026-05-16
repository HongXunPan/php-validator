<?php

namespace HongXunPan\Validator\Tests\Cases\Context;

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Tests\TestCase;
use ReflectionMethod;

class RuleContextContractTest extends TestCase
{
    public function testConstructorDoesNotExposeInternalTypeHints()
    {
        $constructor = new ReflectionMethod(RuleContext::class, '__construct');
        $parameters = $constructor->getParameters();

        foreach ($parameters as $parameter) {
            $class = $parameter->getClass();
            if ($class === null) {
                continue;
            }

            $this->assertSame(false, strpos($class->getName(), '\\Internal\\'), 'RuleContext 公共构造不应暴露 Internal 类型：' . $class->getName());
        }
    }
}
