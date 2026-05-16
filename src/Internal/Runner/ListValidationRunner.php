<?php

namespace HongXunPan\Validator\Internal\Runner;

use HongXunPan\Validator\Context\ValidationOptions;
use HongXunPan\Validator\Internal\Output\ListValidationOutput;
use HongXunPan\Validator\Internal\Path\PathAccessor;

class ListValidationRunner
{
    /**
     * @var ObjectValidationRunner
     */
    private $objectRunner;
    /**
     * @var ScalarListItemRunner
     */
    private $scalarListItemRunner;
    /**
     * @var PathAccessor
     */
    private $pathAccessor;

    /**
     * @param ObjectValidationRunner $objectRunner
     */
    public function __construct(ObjectValidationRunner $objectRunner)
    {
        $this->objectRunner = $objectRunner;
        $this->pathAccessor = new PathAccessor();
        $this->scalarListItemRunner = new ScalarListItemRunner(
            $objectRunner->targetRulePlanCompiler(),
            $objectRunner->targetPlanExecutor(),
            $objectRunner->pathAccessor()
        );
    }

    /**
     * @param array<int, mixed> $list
     * @param string|array<string, string> $rules
     * @param ValidationOptions $options
     *
     * @return \HongXunPan\Validator\Result\ValidationResult
     */
    public function run(array $list, $rules, ValidationOptions $options)
    {
        $output = new ListValidationOutput();
        $position = 0;

        foreach ($list as $item) {
            $position++;
            $itemPrefix = $this->pathAccessor->join($options->fieldPrefix(), (string)$position);

            if (is_array($rules)) {
                if (!is_array($item)) {
                    $output->addListItemTypeError($itemPrefix, $item);
                    continue;
                }

                $output->mergeObjectOutput(
                    $this->objectRunner->runOutput(
                        $item,
                        $rules,
                        $options->withFieldPrefix($itemPrefix),
                        true
                    )
                );

                continue;
            }

            $output->mergeScalarOutput(
                $this->scalarListItemRunner->runOutput($item, $rules, $itemPrefix)
            );
        }

        return $output->toValidationResult();
    }
}
