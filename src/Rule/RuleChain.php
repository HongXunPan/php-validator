<?php

namespace HongXunPan\Validator\Rule;

use HongXunPan\Validator\Exception\InvalidRuleArgumentException;

final class RuleChain
{
    /**
     * @var array<int, string>
     */
    private $rules = array();

    /**
     * @param array<int, mixed> $rules
     */
    private function __construct(array $rules)
    {
        $this->rules = $this->normalizeRules($rules);
        if (empty($this->rules)) {
            throw new InvalidRuleArgumentException('规则链不能为空');
        }
    }

    /**
     * @param array<int, mixed> $rules
     *
     * @return self
     */
    public static function join(array $rules)
    {
        return new self($rules);
    }

    /**
     * @param bool $condition
     * @param mixed $rule
     *
     * @return mixed|null
     */
    public static function when($condition, $rule)
    {
        return $condition ? $rule : null;
    }

    /**
     * @param bool $condition
     * @param mixed $rule
     *
     * @return mixed|null
     */
    public static function whenNot($condition, $rule)
    {
        return !$condition ? $rule : null;
    }

    /**
     * @param mixed $rule
     *
     * @return $this
     */
    public function append($rule)
    {
        $rules = $this->normalizeRules(array($rule));
        foreach ($rules as $normalizedRule) {
            $this->rules[] = $normalizedRule;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode('|', $this->rules);
    }

    /**
     * @param array<int, mixed> $rules
     *
     * @return array<int, string>
     */
    private function normalizeRules(array $rules)
    {
        $normalized = array();
        foreach ($rules as $rule) {
            if ($rule === null) {
                continue;
            }

            if (is_array($rule)) {
                $normalized = array_merge($normalized, $this->normalizeRules($rule));
                continue;
            }

            if (is_bool($rule) || is_resource($rule)) {
                throw new InvalidRuleArgumentException('规则链只允许字符串或可转换为字符串的规则 token');
            }

            if (is_object($rule) && !method_exists($rule, '__toString')) {
                throw new InvalidRuleArgumentException('规则链只允许字符串或可转换为字符串的规则 token');
            }

            $rule = trim((string)$rule);
            if ($rule === '') {
                continue;
            }

            if (strpos($rule, '|') !== false) {
                throw new InvalidRuleArgumentException('规则链单个 token 不能包含 |，请传入未组合的单条规则');
            }

            $normalized[] = $rule;
        }

        return $normalized;
    }
}
