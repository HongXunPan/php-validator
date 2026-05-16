<?php

namespace HongXunPan\Validator\Tests\Cases\Internal\Output;

use HongXunPan\Validator\Internal\Output\ListValidationOutput;
use HongXunPan\Validator\Internal\Output\ScalarValidationOutput;
use HongXunPan\Validator\Internal\Output\ValidationOutput;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Tests\TestCase;

class ListValidationOutputTest extends TestCase
{
    public function testAddListItemTypeErrorBuildsFailureResult()
    {
        $output = new ListValidationOutput();
        $output->addListItemTypeError('items.2', 'broken');

        $result = $output->toValidationResult();

        $this->assertTrue($result->isFailed(), '追加列表项类型错误后结果应失败');
        $this->assertSame('items.2', $result->detail()[0]['param'], '错误路径应正确');
    }

    public function testMergeScalarItemResultCollectsNormalizedValue()
    {
        $output = new ListValidationOutput();
        $itemOutput = new ValidationOutput(new PathAccessor());
        $target = new RuleTarget('__item__', '值');
        $targetValueContext = new TargetValueContext(true, 'Alice');
        $targetValueContext->commitOutputValue(true);
        $itemOutput->writeValidatedTarget($target, $targetValueContext);

        $output->mergeScalarOutput(new ScalarValidationOutput($itemOutput, '__item__'));

        $result = $output->toValidationResult();

        $this->assertTrue($result->isPassed(), '成功合并标量项后结果应通过');
        $this->assertSame(array('Alice'), $result->validatedData(), '标量项应提取归一化值写入列表结果');
    }
}
