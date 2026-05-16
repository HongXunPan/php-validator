<?php

namespace HongXunPan\Validator\Internal\Input;

use HongXunPan\Validator\Internal\Path\PathAccessor;

class RawInputSource
{
    /**
     * @var array
     */
    private $rawData;
    /**
     * @var PathAccessor
     */
    private $pathAccessor;

    /**
     * @param array<string, mixed> $rawData
     * @param PathAccessor $pathAccessor
     */
    public function __construct(array $rawData, PathAccessor $pathAccessor)
    {
        $this->rawData = $rawData;
        $this->pathAccessor = $pathAccessor;
    }

    /**
     * @param string $fieldPath
     * @param bool $strict
     *
     * @return \HongXunPan\Validator\Context\PathValue
     */
    public function pathValue($fieldPath, $strict)
    {
        return $this->pathAccessor->getValue($this->rawData, $fieldPath, $strict);
    }
}
