<?php

namespace HongXunPan\Validator\Context;

/**
 * ValidationOptions 承接验证选项。
 *
 * @phpstan-consistent-constructor
 */
class ValidationOptions
{
    /**
     * @var bool
     */
    private $strict;
    /**
     * @var bool
     */
    private $rejectUnknown;
    /**
     * @var string
     */
    private $fieldPrefix;
    /**
     * @var array
     */
    private $extra;

    /**
     * @param bool $strict
     * @param bool $rejectUnknown
     * @param string $fieldPrefix
     * @param array<string, mixed> $extra
     */
    public function __construct($strict, $rejectUnknown, $fieldPrefix, array $extra)
    {
        $this->strict = (bool)$strict;
        $this->rejectUnknown = (bool)$rejectUnknown;
        $this->fieldPrefix = (string)$fieldPrefix;
        $this->extra = $extra;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return static
     */
    public static function fromArray(array $options)
    {
        $strict = array_key_exists('strict', $options) ? $options['strict'] : true;
        $rejectUnknown = array_key_exists('reject_unknown', $options) ? $options['reject_unknown'] : false;
        $fieldPrefix = array_key_exists('field_prefix', $options) ? $options['field_prefix'] : '';

        $extra = $options;
        unset($extra['strict'], $extra['reject_unknown'], $extra['field_prefix']);

        return new static($strict, $rejectUnknown, $fieldPrefix, $extra);
    }

    /**
     * @return bool
     */
    public function strict()
    {
        return $this->strict;
    }

    /**
     * @return bool
     */
    public function rejectUnknown()
    {
        return $this->rejectUnknown;
    }

    /**
     * @return string
     */
    public function fieldPrefix()
    {
        return $this->fieldPrefix;
    }

    /**
     * @return array<string, mixed>
     */
    public function extra()
    {
        return $this->extra;
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->extra) ? $this->extra[$key] : $default;
    }

    /**
     * @param string $fieldPrefix
     *
     * @return static
     */
    public function withFieldPrefix($fieldPrefix)
    {
        return new static(
            $this->strict,
            $this->rejectUnknown,
            $fieldPrefix,
            $this->extra
        );
    }

    /**
     * @return static
     */
    public static function forScalarListItem()
    {
        return new static(true, false, '', array());
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return array_merge(array(
            'strict' => $this->strict,
            'reject_unknown' => $this->rejectUnknown,
            'field_prefix' => $this->fieldPrefix,
        ), $this->extra);
    }
}
