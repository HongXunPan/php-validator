<?php

namespace HongXunPan\Validator\Tests\Cases\Internal\Output;

use HongXunPan\Validator\Internal\Detail\ValidationDetailItem;
use HongXunPan\Validator\Internal\Output\ValidationOutput;
use HongXunPan\Validator\Internal\Path\PathAccessor;
use HongXunPan\Validator\Internal\Target\RuleTarget;
use HongXunPan\Validator\Internal\Context\TargetValueContext;
use HongXunPan\Validator\Tests\TestCase;

class ValidationOutputTest extends TestCase
{
    public function testWriteValidatedTargetUsesTargetContextOutputValue()
    {
        $output = new ValidationOutput(new PathAccessor());
        $target = new RuleTarget('profile.name', '姓名');
        $context = new TargetValueContext(true, ' Alice ');
        $context->commitOutputValue(false);

        $output->writeValidatedTarget($target, $context);
        $result = $output->toValidationResult();

        $this->assertSame(' Alice ', $result->validatedData()['profile']['name'], '输出值应从 target 上下文提交到结果');
    }

    public function testAppendFailureBuildsFailureResult()
    {
        $output = new ValidationOutput(new PathAccessor());
        $output->appendFailure(
            '姓名 is required',
            ValidationDetailItem::ruleFailed('姓名', null, 'required', '')
        );

        $result = $output->toValidationResult();

        $this->assertTrue($result->isFailed(), '追加错误明细后结果应失败');
        $this->assertSame('姓名 is required', $result->errors()[0], '错误消息应保留追加值');
    }
}
