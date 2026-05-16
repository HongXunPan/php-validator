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

    /**
     * @param RawInputSource $rawInputSource
     * @param TargetValueContextStore $targetValueContextStore
     */
    public function __construct(RawInputSource $rawInputSource, TargetValueContextStore $targetValueContextStore)
    {
        $this->rawInputSource = $rawInputSource;
        $this->targetValueContextStore = $targetValueContextStore;
    }

    /**
     * @param string $targetPath
     * @param bool $strict
     *
     * @return \HongXunPan\Validator\Context\PathValue
     */
    public function rawPathValue($targetPath, $strict)
    {
        $rawTargetValue = $this->targetValueContextStore->rawPathValue($targetPath);
        if ($rawTargetValue->exists()) {
            return $rawTargetValue;
        }

        return $this->rawInputSource->pathValue($targetPath, $strict);
    }

    /**
     * @param string $targetPath
     *
     * @return \HongXunPan\Validator\Context\PathValue
     */
    public function materializedPathValue($targetPath)
    {
        return $this->targetValueContextStore->materializedPathValue($targetPath);
    }

    /**
     * @param string $targetPath
     *
     * @return \HongXunPan\Validator\Context\PathValue
     */
    public function dependentPathValue($targetPath)
    {
        return $this->targetValueContextStore->dependentPathValue($targetPath);
    }
}
