<?php

namespace HongXunPan\Validator\Internal\Context;

use HongXunPan\Validator\Context\PathValue;

class TargetValueContextStore
{
    /**
     * @var TargetValueContext[]
     */
    private $contextMap = array();

    /**
     * @param string $targetPath
     */
    public function remember($targetPath, TargetValueContext $targetValueContext)
    {
        $this->contextMap[(string)$targetPath] = $targetValueContext;
    }

    /**
     * @param string $targetPath
     *
     * @return bool
     */
    public function has($targetPath)
    {
        return array_key_exists((string)$targetPath, $this->contextMap);
    }

    /**
     * @param string $targetPath
     *
     * @return TargetValueContext|null
     */
    public function get($targetPath)
    {
        $targetPath = (string)$targetPath;
        if (!$this->has($targetPath)) {
            return null;
        }

        return $this->contextMap[$targetPath];
    }

    /**
     * @param string $targetPath
     *
     * @return bool
     */
    public function hasMaterialized($targetPath)
    {
        $targetValueContext = $this->get($targetPath);

        return $targetValueContext instanceof TargetValueContext
            && $targetValueContext->isMaterialized();
    }

    /**
     * @param string $targetPath
     *
     * @return PathValue
     */
    public function rawPathValue($targetPath)
    {
        $targetValueContext = $this->get($targetPath);
        if (!$targetValueContext instanceof TargetValueContext) {
            return new PathValue(false, null);
        }

        return new PathValue(
            $targetValueContext->rawExists(),
            $targetValueContext->rawValue()
        );
    }

    /**
     * @param string $targetPath
     *
     * @return PathValue
     */
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

    /**
     * @param string $targetPath
     *
     * @return PathValue
     */
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
