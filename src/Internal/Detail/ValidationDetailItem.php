<?php

namespace HongXunPan\Validator\Internal\Detail;

/**
 * @phpstan-consistent-constructor
 */
class ValidationDetailItem
{
    const RULE_UNKNOWN = 'unknown';
    const RULE_ARRAY = 'array';
    const RULE_UNSUPPORTED = 'unsupported';

    const REASON_UNKNOWN_FIELD = 'unknown field';
    const REASON_LIST_ITEM_NOT_ARRAY = 'list item not array';
    const REASON_RULE_NOT_SUPPORT = 'rule not support';
    const REASON_RULE_FAILED = 'result: false';

    /**
     * @var string
     */
    private $fieldPath;
    /**
     * @var string
     */
    private $param;
    /**
     * @var mixed
     */
    private $value;
    /**
     * @var string
     */
    private $rule;
    /**
     * @var string
     */
    private $ruleValue;
    /**
     * @var string
     */
    private $reason;

    /**
     * @param string $fieldPath
     * @param string $param
     * @param mixed $value
     * @param string $rule
     * @param string $ruleValue
     * @param string $reason
     */
    private function __construct($fieldPath, $param, $value, $rule, $ruleValue, $reason)
    {
        $this->fieldPath = (string)$fieldPath;
        $this->param = (string)$param;
        $this->value = $value;
        $this->rule = (string)$rule;
        $this->ruleValue = (string)$ruleValue;
        $this->reason = (string)$reason;
    }

    /**
     * @param string $fieldPath
     * @param string $param
     * @param mixed $value
     * @param string $rule
     * @param string $ruleValue
     * @param string $reason
     *
     * @return static
     */
    public static function create($fieldPath, $param, $value, $rule, $ruleValue, $reason)
    {
        return new static($fieldPath, $param, $value, $rule, $ruleValue, $reason);
    }

    /**
     * @param string $fieldPath
     * @param string $param
     * @param mixed $value
     *
     * @return static
     */
    public static function unknownField($fieldPath, $param, $value)
    {
        return new static(
            $fieldPath,
            $param,
            $value,
            self::RULE_UNKNOWN,
            '',
            self::REASON_UNKNOWN_FIELD
        );
    }

    /**
     * @param string $fieldPath
     * @param string $param
     * @param mixed $value
     *
     * @return static
     */
    public static function listItemNotArray($fieldPath, $param, $value)
    {
        return new static(
            $fieldPath,
            $param,
            $value,
            self::RULE_ARRAY,
            '',
            self::REASON_LIST_ITEM_NOT_ARRAY
        );
    }

    /**
     * @param string $fieldPath
     * @param string $param
     * @param mixed $value
     * @param string $ruleValue
     *
     * @return static
     */
    public static function unsupportedRule($fieldPath, $param, $value, $ruleValue)
    {
        return new static(
            $fieldPath,
            $param,
            $value,
            self::RULE_UNSUPPORTED,
            $ruleValue,
            self::REASON_RULE_NOT_SUPPORT
        );
    }

    /**
     * @param string $fieldPath
     * @param string $param
     * @param mixed $value
     * @param string $rule
     * @param string $ruleValue
     *
     * @return static
     */
    public static function ruleFailed($fieldPath, $param, $value, $rule, $ruleValue)
    {
        return new static(
            $fieldPath,
            $param,
            $value,
            $rule,
            $ruleValue,
            self::REASON_RULE_FAILED
        );
    }

    /**
     * @return string
     */
    public function param()
    {
        return $this->param;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function rule()
    {
        return $this->rule;
    }

    /**
     * @return string
     */
    public function ruleValue()
    {
        return $this->ruleValue;
    }

    /**
     * @return string
     */
    public function reason()
    {
        return $this->reason;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return array(
            'field_path' => $this->fieldPath,
            'param' => $this->param,
            'value' => $this->value,
            'rule' => $this->rule,
            'rule_value' => $this->ruleValue,
            'reason' => $this->reason,
        );
    }
}
