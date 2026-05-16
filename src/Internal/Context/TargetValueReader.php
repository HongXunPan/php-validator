<?php

namespace HongXunPan\Validator\Internal\Context;

use HongXunPan\Validator\Context\RuleValueReaderInterface;
use HongXunPan\Validator\Internal\Input\RawInputSource;

class TargetValueReader implements RuleValueReaderInterface
{
    /**
     * @var RawInputSource
     */
    private $rawInputSource;
    /**
     * @var TargetValueContextStore
     */
    private $targetValueContextStore;

    public function __construct(RawInputSource $rawInputSource, TargetValueContextStore $targetValueContextStore)
    {
        $this->rawInputSource = $rawInputSource;
        $this->targetValueContextStore = $targetValueContextStore;
    }

    public function rawPathValue($targetPath, $strict)
    {
        $rawTargetValue = $this->targetValueContextStore->rawPathValue($targetPath);
        if ($rawTargetValue->exists()) {
            return $rawTargetValue;
        }

        return $this->rawInputSource->pathValue($targetPath, $strict);
    }

    public function materializedPathValue($targetPath)
    {
        return $this->targetValueContextStore->materializedPathValue($targetPath);
    }

    public function dependentPathValue($targetPath)
    {
        return $this->targetValueContextStore->dependentPathValue($targetPath);
    }
}
