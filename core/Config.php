<?php

declare(strict_types = 1);

namespace Core;

final class Config {
    public static function get(string $key, mixed $default = null): mixed {
        $segments = explode(".", $key);
        $value = $GLOBALS["config"] ?? [];

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}