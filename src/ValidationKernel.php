<?php

namespace HongXunPan\Validator;

use HongXunPan\Validator\Context\ValidationOptions;
use HongXunPan\Validator\Internal\Rules\RuleSet;
use HongXunPan\Validator\Internal\Runner\ListValidationRunner;
use HongXunPan\Validator\Internal\Runner\ObjectValidationRunner;
use HongXunPan\Validator\Output\ArrayAccessValidatedDataWriter;
use HongXunPan\Validator\Result\ValidationResult;

/**
 * @phpstan-consistent-constructor
 */
class ValidationKernel
{
    /**
     * @var RuleSet
     */
    private $ruleSet;
    /**
     * @var ObjectValidationRunner
     */
    private $objectRunner;
    /**
     * @var ListValidationRunner
     */
    private $listRunner;
    /**
     * @var ArrayAccessValidatedDataWriter
     */
    private $validatedDataWriter;

    public function __construct(RuleSet $ruleSet)
    {
        $this->ruleSet = $ruleSet;
        $this->objectRunner = new ObjectValidationRunner($ruleSet);
        $this->listRunner = new ListValidationRunner($this->objectRunner);
        $this->validatedDataWriter = new ArrayAccessValidatedDataWriter();
    }

    public static function create($validatorClass)
    {
        return new static(RuleSet::fromValidatorClass($validatorClass));
    }

    public function validate(array $data, array $rules, array $options = array())
    {
        return $this->objectRunner->run(
            $data,
            $rules,
            ValidationOptions::fromArray($options),
            false
        );
    }

    public function validateAndNormalize(array $data, array $rules, array $options = array())
    {
        return $this->objectRunner->run(
            $data,
            $rules,
            ValidationOptions::fromArray($options),
            true
        );
    }

    public function validateListAndNormalize(array $list, $rules, array $options = array())
    {
        return $this->listRunner->run(
            $list,
            $rules,
            ValidationOptions::fromArray($options)
        );
    }

    public function writeValidatedDataTo(ValidationResult $result, $target)
    {
        return $this->validatedDataWriter->write($result, $target);
    }

    public function validateAndWriteTo(array $data, array $rules, $target, array $options = array())
    {
        $result = $this->validateAndNormalize($data, $rules, $options);
        $this->writeValidatedDataTo($result, $target);

        return $result;
    }
}
