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

    public function __construct(array $rawData, PathAccessor $pathAccessor)
    {
        $this->rawData = $rawData;
        $this->pathAccessor = $pathAccessor;
    }

    public function pathValue($fieldPath, $strict)
    {
        return $this->pathAccessor->getValue($this->rawData, $fieldPath, $strict);
    }
}
