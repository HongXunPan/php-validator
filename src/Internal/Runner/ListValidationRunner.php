<?php

namespace HongXunPan\Validator\Internal\Runner;

use HongXunPan\Validator\Context\ValidationOptions;
use HongXunPan\Validator\Result\ValidationResult;
use HongXunPan\Validator\Rule\Type\ArrayType;
use HongXunPan\Validator\Support\PathAccessor;

class ListValidationRunner
{
    /**
     * @var ObjectValidationRunner
     */
    private $objectRunner;
    /**
     * @var PathAccessor
     */
    private $pathAccessor;

    public function __construct(ObjectValidationRunner $objectRunner)
    {
        $this->objectRunner = $objectRunner;
        $this->pathAccessor = new PathAccessor();
    }

    public function run(array $list, $rules, ValidationOptions $options)
    {
        $errors = array();
        $detail = array();
        $validatedData = array();
        $position = 0;

        foreach ($list as $item) {
            $position++;
            $itemPrefix = $this->pathAccessor->join($options->fieldPrefix(), (string)$position);

            if (is_array($rules)) {
                if (!is_array($item)) {
                    $errors[] = str_replace('$paramName', $itemPrefix, ArrayType::defaultMessage());
                    $detail[] = array(
                        'param' => $itemPrefix,
                        'value' => $item,
                        'rule' => 'array',
                        'rule_value' => '',
                        'reason' => 'list item not array',
                    );
                    continue;
                }

                $itemResult = $this->objectRunner->run(
                    $item,
                    $rules,
                    ValidationOptions::fromArray(array_merge($options->toArray(), array(
                        'field_prefix' => $itemPrefix,
                    ))),
                    true
                );

                $errors = array_merge($errors, $itemResult->errors());
                $detail = array_merge($detail, $itemResult->detail());

                if ($itemResult->isPassed()) {
                    $validatedData[] = $itemResult->validatedData();
                }

                continue;
            }

            $itemResult = $this->objectRunner->run(
                array('value' => $item),
                array('value:' . $itemPrefix => $rules),
                ValidationOptions::fromArray(array(
                    'strict' => true,
                    'reject_unknown' => false,
                    'field_prefix' => '',
                )),
                true
            );

            $errors = array_merge($errors, $itemResult->errors());
            $detail = array_merge($detail, $itemResult->detail());

            if ($itemResult->isPassed()) {
                $normalizedData = $itemResult->validatedData();
                $validatedData[] = array_key_exists('value', $normalizedData)
                    ? $normalizedData['value']
                    : null;
            }
        }

        if (empty($errors)) {
            return ValidationResult::success($validatedData);
        }

        return ValidationResult::failure($errors, $detail, $validatedData);
    }
}
