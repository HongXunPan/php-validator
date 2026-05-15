<?php

namespace HongXunPan\Validator\Internal\Target;

use HongXunPan\Validator\Internal\Path\PathValue;

class TargetValueContextStore
{
    /**
     * @var TargetValueContext[]
     */
    private $contextMap = array();

    public function remember($targetPath, TargetValueContext $targetValueContext)
    {
        $this->contextMap[(string)$targetPath] = $targetValueContext;
    }

    public function has($targetPath)
    {
        return array_key_exists((string)$targetPath, $this->contextMap);
    }

    public function get($targetPath)
    {
        $targetPath = (string)$targetPath;
        if (!$this->has($targetPath)) {
            return null;
        }

        return $this->contextMap[$targetPath];
    }

    public function hasMaterialized($targetPath)
    {
        $targetValueContext = $this->get($targetPath);

        return $targetValueContext instanceof TargetValueContext
            && $targetValueContext->isMaterialized();
    }

    public function materializedPathValue($targetPath)
    {
        $targetValueContext = $this->get($targetPath);
        if (!$targetValueContext instanceof TargetValueContext || !$targetValueContext->isMaterialized()) {
            return new PathValue(false, null);
        }

        return new PathValue(
            $targetValueContext->materializedExists(),
            $targetValueContext->materializedValue()
        );
    }

    public function dependentPathValue($targetPath)
    {
        $targetValueContext = $this->get($targetPath);
        if (
            !$targetValueContext instanceof TargetValueContext
            || !$targetValueContext->isMaterialized()
            || !$targetValueContext->isDependentReadable()
        ) {
            return new PathValue(false, null);
        }

        return new PathValue(
            $targetValueContext->materializedExists(),
            $targetValueContext->materializedValue()
        );
    }
}
