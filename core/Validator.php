<?php

declare(strict_types=1);

namespace Core;

final class Validator
{
    private array $data;
    private array $rules;
    private array $messages;
    private array $errors = [];
    private array $validated = [];

    public function __construct(array $data, array $rules, array $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
    }

    public static function make(array $data, array $rules, array $messages = []): self
    {
        return new self($data, $rules, $messages);
    }

    public function passes(): bool
    {
        $this->errors = [];
        $this->validated = [];

        foreach ($this->rules as $field => $ruleSet) {
            $ruleList = $this->normalizeRules($ruleSet);

            $fieldsToValidate = str_contains($field, '*')
                ? $this->expandWildcardPaths($field, $this->data)
                : [$field];

            // Si el patrón tiene wildcard pero no encontró coincidencias,
            // aún puede ser válido si el campo padre es nullable.
            if (empty($fieldsToValidate)) {
                continue;
            }

            foreach ($fieldsToValidate as $realField) {
                $valueExists = false;
                $value = $this->getValueByPath($this->data, $realField, $valueExists);

                $isNullable = in_array('nullable', $ruleList, true);

                if ($isNullable && ($value === null || $value === '')) {
                    $this->setValueByPath($this->validated, $realField, $value);
                    continue;
                }

                foreach ($ruleList as $rule) {
                    [$name, $param] = $this->parseRule($rule);

                    if ($name === 'nullable') {
                        continue;
                    }

                    $ok = $this->applyRule($realField, $name, $param, $value, $valueExists);

                    if (!$ok && ($name === 'required' || $name === 'required_if')) {
                        break;
                    }
                }

                if (!isset($this->errors[$realField])) {
                    $this->setValueByPath($this->validated, $realField, $value);
                }
            }
        }

        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    // Retorna el primer error del campo de errores, o null si no hay errores
    public function first() {
        return $this->errors ? reset($this->errors)[0] : null;
    }

    public function validated(): array
    {
        // Asegura que se haya ejecutado passes() al menos una vez
        return $this->validated;
    }

    private function normalizeRules(string|array $rules): array
    {
        if (is_array($rules)) {
            return $rules;
        }

        $rules = trim($rules);
        if ($rules === '') {
            return [];
        }

        return array_map('trim', explode('|', $rules));
    }

    private function parseRule(string $rule): array
    {
        if (!str_contains($rule, ':')) {
            return [$rule, null];
        }

        [$name, $param] = explode(':', $rule, 2);
        return [trim($name), trim($param)];
    }

    private function addError(string $field, string $rule, string $defaultMessage): void
    {
        $key = $field . '.' . $rule;

        $wildcardKey = preg_replace('/\.\d+\./', '.*.', $key);
        $wildcardKey = preg_replace('/\.\d+$/', '.*', $wildcardKey);

        $message = $this->messages[$key]
            ?? $this->messages[$wildcardKey]
            ?? $defaultMessage;

        $this->errors[$field][] = $message;
    }

    private function applyRule(string $field, string $name, ?string $param, mixed $value, bool $valueExists): bool
    {
        switch ($name) {
            case 'required':
                if (!$valueExists) {
                    $this->addError($field, 'required', "The {$field} field is required.");
                    return false;
                }
                if (is_string($value) && trim($value) === '') {
                    $this->addError($field, 'required', "The {$field} field is required.");
                    return false;
                }
                if (is_array($value) && count($value) === 0) {
                    $this->addError($field, 'required', "The {$field} field is required.");
                    return false;
                }
                return true;
            case 'required_if':
                if ($param === null) {
                    $this->addError($field, 'required_if', "The {$field} has an invalid required_if rule.");
                    return false;
                }

                [$otherField, $expectedValue] = array_map('trim', explode(',', $param, 2) + [null, null]);

                $resolvedOtherField = $this->resolveRelativeField($field, $otherField);
                $otherExists = false;
                $otherValue = $this->getValueByPath($this->data, $resolvedOtherField, $otherExists);

                if ((string)$otherValue === $expectedValue) {
                    if (!$valueExists || $value === null || (is_string($value) && trim($value) === '')) {
                        $this->addError($field, 'required_if', "The {$field} field is required.");
                        return false;
                    }
                }

                return true;
            case 'required_unless':
                if ($param === null) {
                    $this->addError($field, 'required_unless', "The {$field} has an invalid required_unless rule.");
                    return false;
                }

                [$otherField, $expectedValue] = array_map('trim', explode(',', $param, 2) + [null, null]);

                $resolvedOtherField = $this->resolveRelativeField($field, $otherField);
                $otherExists = false;
                $otherValue = $this->getValueByPath($this->data, $resolvedOtherField, $otherExists);

                if ((string)$otherValue !== $expectedValue) {
                    if (!$valueExists || $value === null || (is_string($value) && trim($value) === '')) {
                        $this->addError($field, 'required_unless', "The {$field} field is required.");
                        return false;
                    }
                }

                return true;

            case 'email':
                if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'email', "The {$field} must be a valid email.");
                    return false;
                }
                return true;

            case 'min':
                $min = (int)($param ?? 0);
                if (is_numeric($value)) {
                    if ((float)$value < $min) {
                        $this->addError($field, 'min', "The {$field} must be at least {$min}.");
                        return false;
                    }
                    return true;
                }
                if (is_string($value)) {
                    if (mb_strlen($value) < $min) {
                        $this->addError($field, 'min', "The {$field} must be at least {$min} characters.");
                        return false;
                    }
                    return true;
                }
                if (is_array($value)) {
                    if (count($value) < $min) {
                        $this->addError($field, 'min', "The {$field} must have at least {$min} items.");
                        return false;
                    }
                    return true;
                }
                $this->addError($field, 'min', "The {$field} is invalid.");
                return false;

            case 'max':
                $max = (int)($param ?? 0);

                if (is_numeric($value)) {
                    if ((float)$value > $max) {
                        $this->addError($field, 'max', "The {$field} must be at most {$max}.");
                        return false;
                    }
                    return true;
                }

                if (is_string($value)) {
                    if (mb_strlen($value) > $max) {
                        $this->addError($field, 'max', "The {$field} must be at most {$max} characters.");
                        return false;
                    }
                    return true;
                }

                if (is_array($value) && isset($value['size'], $value['name'])) {
                    $sizeInKb = (int)ceil(((int)$value['size']) / 1024);

                    if ($sizeInKb > $max) {
                        $this->addError($field, 'max', "The {$field} must not be greater than {$max} KB.");
                        return false;
                    }

                    return true;
                }

                if (is_array($value)) {
                    if (count($value) > $max) {
                        $this->addError($field, 'max', "The {$field} must have at most {$max} items.");
                        return false;
                    }
                    return true;
                }

                $this->addError($field, 'max', "The {$field} is invalid.");
                return false;

            case 'numeric':
                if (!is_numeric($value)) {
                    $this->addError($field, 'numeric', "The {$field} must be numeric.");
                    return false;
                }
                return true;

            case 'integer':
                if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->addError($field, 'integer', "The {$field} must be an integer.");
                    return false;
                }
                return true;
            case 'integer_or_literal':
                $literal = (string)($param ?? '');

                if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
                    return true;
                }

                if ((string)$value === $literal) {
                    return true;
                }

                $this->addError($field, 'integer_or_literal', "The {$field} must be an integer or '{$literal}'.");
                return false;
            case 'file':
                if (!is_array($value)) {
                    $this->addError($field, 'file', "The {$field} must be a valid file.");
                    return false;
                }

                $requiredKeys = ['name', 'type', 'tmp_name', 'error', 'size'];
                foreach ($requiredKeys as $requiredKey) {
                    if (!array_key_exists($requiredKey, $value)) {
                        $this->addError($field, 'file', "The {$field} must be a valid file.");
                        return false;
                    }
                }

                if ((int)$value['error'] !== UPLOAD_ERR_OK) {
                    $this->addError($field, 'file', "The {$field} upload failed.");
                    return false;
                }

                return true;

            case 'boolean':
                if (is_bool($value)) {
                    return true;
                }
                if (is_numeric($value)) {
                    return in_array((string)$value, ['0', '1'], true);
                }
                if (is_string($value)) {
                    $v = strtolower(trim($value));
                    if (!in_array($v, ['0', '1', 'true', 'false', 'yes', 'no', 'on', 'off'], true)) {
                        $this->addError($field, 'boolean', "The {$field} must be boolean.");
                        return false;
                    }
                    return true;
                }
                $this->addError($field, 'boolean', "The {$field} must be boolean.");
                return false;
            case 'alpha':
                if (!is_string($value) || !preg_match('/^\p{L}+$/u', $value)) {
                    $this->addError($field, 'alpha', "The {$field} may only contain letters.");
                    return false;
                }
                return true;

            case 'alpha_spaces':
                if (!is_string($value) || !preg_match('/^[\p{L}\s]+$/u', trim($value))) {
                    $this->addError($field, 'alpha_spaces', "The {$field} may only contain letters and spaces.");
                    return false;
                }
                return true;
            case 'alpha_dash':
                if (!is_string($value) || !preg_match('/^[\p{L}\p{N}_-]+$/u', $value)) {
                    $this->addError($field, 'alpha_dash', "The {$field} may only contain letters, numbers, dashes and underscores.");
                    return false;
                }
                return true;
            case 'alpha_num':
                if (!is_string($value) || !preg_match('/^[\p{L}\p{N}]+$/u', $value)) {
                    $this->addError($field, 'alpha_num', "The {$field} may only contain letters and numbers.");
                    return false;
                }
                return true;
            case 'alpha_num_spaces':
                if (!is_string($value) || !preg_match('/^[\p{L}\p{N}\s]+$/u', trim($value))) {
                    $this->addError($field, 'alpha_num_spaces', "The {$field} may only contain letters, numbers and spaces.");
                    return false;
                }
                return true;
            case 'array':
                if (!is_array($value)) {
                    $this->addError($field, 'array', "The {$field} must be an array.");
                    return false;
                }
                return true;
            case 'in':
                $allowed = array_map('trim', explode(',', (string)$param));
                if (!in_array((string)$value, $allowed, true)) {
                    $this->addError($field, 'in', "The {$field} must be one of: " . implode(', ', $allowed) . '.');
                    return false;
                }
                return true;

            case 'same':
                $other = (string)$param;
                $resolvedOther = $this->resolveRelativeField($field, $other);
                $otherExists = false;
                $otherValue = $this->getValueByPath($this->data, $resolvedOther, $otherExists);

                if ($value !== $otherValue) {
                    $this->addError($field, 'same', "The {$field} must match {$resolvedOther}.");
                    return false;
                }
                return true;
            case 'different':
                $other = (string)$param;
                $resolvedOther = $this->resolveRelativeField($field, $other);
                $otherExists = false;
                $otherValue = $this->getValueByPath($this->data, $resolvedOther, $otherExists);

                if ($value === $otherValue) {
                    $this->addError($field, 'different', "The {$field} must be different from {$resolvedOther}.");
                    return false;
                }
                return true;

            case 'confirmed':
                $confirmationField = $field . '_confirmation';
                $confirmationExists = false;
                $confirmation = $this->getValueByPath($this->data, $confirmationField, $confirmationExists);

                if ($value !== $confirmation) {
                    $this->addError($field, 'confirmed', "The {$field} confirmation does not match.");
                    return false;
                }
                return true;
            case 'mimes':
                if (!is_array($value) || !isset($value['name'])) {
                    $this->addError($field, 'mimes', "The {$field} must be a valid file.");
                    return false;
                }

                $allowed = array_map('strtolower', array_map('trim', explode(',', (string)$param)));
                $extension = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));

                if (!in_array($extension, $allowed, true)) {
                    $this->addError($field, 'mimes', "The {$field} must be a file of type: " . implode(', ', $allowed) . ".");
                    return false;
                }

                return true;
            case 'phone':
                $normalizedPhone = $this->normalizePhone($value);

                if ($normalizedPhone === null) {
                    $this->addError($field, 'phone', "The {$field} must be a valid phone number.");
                    return false;
                }

                $length = mb_strlen($normalizedPhone);

                if ($length < 10 || $length > 15) {
                    $this->addError($field, 'phone', "The {$field} must contain between 10 and 15 digits.");
                    return false;
                }

                return true;
            case 'postal_code':
                if (is_int($value) || is_float($value)) {
                    $value = (string)$value;
                }

                if (!is_string($value)) {
                    $this->addError($field, 'postal_code', "The {$field} must be a valid postal code.");
                    return false;
                }

                $value = trim($value);

                if (!preg_match('/^[0-9]{5}$/', $value)) {
                    $this->addError($field, 'postal_code', "The {$field} must contain exactly 5 digits.");
                    return false;
                }

                return true;

            case 'regex':
                // Se espera regex tipo /.../flags
                if (!is_string($value) || $param === null) {
                    $this->addError($field, 'regex', "The {$field} format is invalid.");
                    return false;
                }
                $pattern = $param;
                if (@preg_match($pattern, '') === false) {
                    $this->addError($field, 'regex', "The {$field} regex is invalid.");
                    return false;
                }
                if (!preg_match($pattern, $value)) {
                    $this->addError($field, 'regex', "The {$field} format is invalid.");
                    return false;
                }
                return true;

            case 'string':
                if (!is_string($value)) {
                    $this->addError($field, 'string', "The {$field} must be a string.");
                    return false;
                }
                return true;

            default:
                // Regla desconocida
                $this->addError($field, $name, "The {$field} has an invalid rule: {$name}.");
                return false;
        }
    }

    private function hasRule(array $ruleList, string $ruleName): bool
    {
        foreach ($ruleList as $rule) {
            [$name] = $this->parseRule($rule);
            if ($name === $ruleName) {
                return true;
            }
        }

        return false;
    }

    private function getValueByPath(array $data, string $path, bool &$exists = false): mixed
    {
        $segments = explode('.', $path);
        $current = $data;

        foreach ($segments as $segment) {
            if (is_array($current) && array_key_exists($segment, $current)) {
                $current = $current[$segment];
                continue;
            }

            $exists = false;
            return null;
        }

        $exists = true;
        return $current;
    }

    private function setValueByPath(array &$data, string $path, mixed $value): void
    {
        $segments = explode('.', $path);
        $current = &$data;

        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }

            $current = &$current[$segment];
        }

        $current = $value;
    }

    private function expandWildcardPaths(string $pattern, array $data): array
    {
        $segments = explode('.', $pattern);
        $results = ['' => $data];

        foreach ($segments as $segment) {
            $nextResults = [];

            foreach ($results as $path => $currentData) {
                if ($segment === '*') {
                    if (!is_array($currentData)) {
                        continue;
                    }

                    foreach ($currentData as $key => $value) {
                        $newPath = $path === '' ? (string)$key : $path . '.' . $key;
                        $nextResults[$newPath] = $value;
                    }
                } else {
                    if (!is_array($currentData) || !array_key_exists($segment, $currentData)) {
                        continue;
                    }

                    $newPath = $path === '' ? $segment : $path . '.' . $segment;
                    $nextResults[$newPath] = $currentData[$segment];
                }
            }

            $results = $nextResults;
        }

        return array_keys($results);
    }

    private function resolveRelativeField(string $currentField, string $otherField): string
    {
        $currentSegments = explode('.', $currentField);
        $otherSegments = explode('.', $otherField);

        foreach ($otherSegments as $i => $segment) {
            if ($segment === '*' && isset($currentSegments[$i])) {
                $otherSegments[$i] = $currentSegments[$i];
            }
        }

        return implode('.', $otherSegments);
    }

    private function normalizePhone(mixed $value): ?string
    {
        if (is_int($value) || is_float($value)) {
            $value = (string)$value;
        }

        if (!is_string($value)) {
            return null;
        }

        $normalized = preg_replace('/[^0-9]/', '', $value);

        return $normalized === '' ? null : $normalized;
    }
}
