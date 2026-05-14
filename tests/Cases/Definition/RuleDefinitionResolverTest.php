<?php

namespace HongXunPan\Validator\Tests\Cases\Definition;

use HongXunPan\Validator\Definition\RuleDefinitionResolver;
use HongXunPan\Validator\Exception\InvalidRuleDefinitionException;
use HongXunPan\Validator\Exception\RuleNameReservedException;
use HongXunPan\Validator\Tests\Fixtures\Source\CountingCoreSource;
use HongXunPan\Validator\Tests\Fixtures\Source\CountingExtraSource;
use HongXunPan\Validator\Tests\Fixtures\Source\EmptySource;
use HongXunPan\Validator\Tests\Fixtures\Source\InvalidReturnSource;
use HongXunPan\Validator\Tests\Fixtures\Source\MismatchedNameSource;
use HongXunPan\Validator\Tests\Fixtures\Source\OverridingExtraSource;
use HongXunPan\Validator\Tests\TestCase;

class RuleDefinitionResolverTest extends TestCase
{
    public function setUp()
    {
        CountingCoreSource::reset();
        CountingExtraSource::reset();
    }

    public function testResolveCachesCoreDefinition()
    {
        $resolver = RuleDefinitionResolver::create(array(), CountingCoreSource::class);

        $first = $resolver->resolve('required');
        $second = $resolver->resolve('required');

        $this->assertSame($first, $second, '同一 rule 的解析结果应命中缓存');
        $this->assertSame(1, CountingCoreSource::callsFor('required'), 'core source 不应被重复解析');
    }

    public function testResolveFallsBackToExtraSource()
    {
        $resolver = RuleDefinitionResolver::create(
            array(CountingExtraSource::class),
            EmptySource::class
        );

        $definition = $resolver->resolve('customValue');

        $this->assertSame('customValue', $definition->name()->value(), 'extra source 应可提供非 core 规则');
        $this->assertSame(1, CountingExtraSource::callsFor('customValue'), 'extra source 应被命中一次');
    }

    public function testResolveRejectsExtraSourceOverrideOfCoreRule()
    {
        $resolver = RuleDefinitionResolver::create(
            array(OverridingExtraSource::class),
            CountingCoreSource::class
        );

        $this->assertThrows(
            RuleNameReservedException::class,
            function () use ($resolver) {
                $resolver->resolve('required');
            },
            '不允许覆盖 core rule'
        );
    }

    public function testResolveRejectsInvalidSourceReturnValue()
    {
        $resolver = RuleDefinitionResolver::create(array(), InvalidReturnSource::class);

        $this->assertThrows(
            InvalidRuleDefinitionException::class,
            function () use ($resolver) {
                $resolver->resolve('broken');
            },
            '返回值非法'
        );
    }

    public function testResolveRejectsMismatchedRuleName()
    {
        $resolver = RuleDefinitionResolver::create(array(), MismatchedNameSource::class);

        $this->assertThrows(
            InvalidRuleDefinitionException::class,
            function () use ($resolver) {
                $resolver->resolve('expectedName');
            },
            '不匹配的规则名'
        );
    }
}
