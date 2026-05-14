<?php

namespace HongXunPan\Validator\Support;

class PathAccessor
{
    public function getValue(array $data, $path, $strict)
    {
        $path = (string)$path;
        if ($path === '') {
            return array(
                'exists' => true,
                'value' => $data,
            );
        }

        $current = $data;
        $segments = explode('.', $path);

        foreach ($segments as $segment) {
            if (!is_array($current)) {
                return array(
                    'exists' => false,
                    'value' => null,
                );
            }

            $exists = $strict
                ? array_key_exists($segment, $current)
                : isset($current[$segment]);

            if (!$exists) {
                return array(
                    'exists' => false,
                    'value' => null,
                );
            }

            $current = $current[$segment];
        }

        return array(
            'exists' => true,
            'value' => $current,
        );
    }

    public function setValue(array &$data, $path, $value)
    {
        $path = (string)$path;
        if ($path === '') {
            return;
        }

        $segments = explode('.', $path);
        $current = &$data;

        foreach ($segments as $index => $segment) {
            $isLast = $index === count($segments) - 1;
            if ($isLast) {
                $current[$segment] = $value;

                return;
            }

            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = array();
            }

            $current = &$current[$segment];
        }
    }

    public function join($prefix, $field)
    {
        $prefix = (string)$prefix;
        $field = (string)$field;

        if ($prefix === '') {
            return $field;
        }

        if ($field === '') {
            return $prefix;
        }

        return $prefix . '.' . $field;
    }

    public function buildDisplayName($fieldName, $prefix)
    {
        $fieldName = (string)$fieldName;
        $prefix = (string)$prefix;

        if ($prefix === '') {
            return $fieldName;
        }

        if ($fieldName === '') {
            return $prefix;
        }

        return $prefix . '.' . $fieldName;
    }
}
