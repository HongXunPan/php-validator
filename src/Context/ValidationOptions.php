<?php

namespace HongXunPan\Validator\Context;

/**
 * ValidationOptions 承接验证选项。
 */
class ValidationOptions
{
    private $strict;
    private $rejectUnknown;
    private $fieldPrefix;
    private $extra;

    public function __construct($strict, $rejectUnknown, $fieldPrefix, array $extra)
    {
        $this->strict = (bool)$strict;
        $this->rejectUnknown = (bool)$rejectUnknown;
        $this->fieldPrefix = (string)$fieldPrefix;
        $this->extra = $extra;
    }

    public static function fromArray(array $options)
    {
        $strict = array_key_exists('strict', $options) ? $options['strict'] : true;
        $rejectUnknown = array_key_exists('reject_unknown', $options) ? $options['reject_unknown'] : false;
        $fieldPrefix = array_key_exists('field_prefix', $options) ? $options['field_prefix'] : '';

        $extra = $options;
        unset($extra['strict'], $extra['reject_unknown'], $extra['field_prefix']);

        return new static($strict, $rejectUnknown, $fieldPrefix, $extra);
    }

    public function strict()
    {
        return $this->strict;
    }

    public function rejectUnknown()
    {
        return $this->rejectUnknown;
    }

    public function fieldPrefix()
    {
        return $this->fieldPrefix;
    }

    public function extra()
    {
        return $this->extra;
    }

    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->extra) ? $this->extra[$key] : $default;
    }

    public function toArray()
    {
        return array_merge(array(
            'strict' => $this->strict,
            'reject_unknown' => $this->rejectUnknown,
            'field_prefix' => $this->fieldPrefix,
        ), $this->extra);
    }
}
