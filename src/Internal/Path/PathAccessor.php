<?php

namespace HongXunPan\Validator\Internal\Path;

class PathAccessor
{
    public function getValue(array $data, $path, $strict)
    {
        $path = (string)$path;
        if ($path === '') {
            return new PathValue(true, $data);
        }

        $current = $data;
        $segments = explode('.', $path);

        foreach ($segments as $segment) {
            if (!is_array($current)) {
                return new PathValue(false, null);
            }

            $exists = $strict
                ? array_key_exists($segment, $current)
                : isset($current[$segment]);

            if (!$exists) {
                return new PathValue(false, null);
            }

            $current = $current[$segment];
        }

        return new PathValue(true, $current);
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
