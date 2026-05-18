<?php

namespace HongXunPan\Validator\Rule\Argument;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

class KeySetArgument
{
    /**
     * @var array<int, string>
     */
    private $keys;

    /**
     * @param array<int, string> $keys
     */
    public function __construct(array $keys)
    {
        if (empty($keys)) {
            throw new InvalidRuleArgumentException('key set 参数不能为空');
        }

        foreach ($keys as $key) {
            if (!is_string($key) || $key === '') {
                throw new InvalidRuleArgumentException('key set 参数只允许非空字符串成员');
            }
        }

        $this->keys = array_values($keys);
    }

    /**
     * @return array<int, string>
     */
    public function keys()
    {
        return $this->keys;
    }
}
