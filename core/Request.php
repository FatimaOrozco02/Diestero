<?php

declare(strict_types=1);

namespace Core;

final class Request
{
      private string $method;
      private string $path;
      private array $query;
      private array $body;
      private array $headers;
      private array $files;

      private function __construct(
            string $method,
            string $path,
            array $query,
            array $body,
            array $headers,
            array $files
      ) {
            $this->method  = strtoupper($method);
            $this->path    = $path;
            $this->query   = $query;
            $this->body    = $body;
            $this->headers = $headers;
            $this->files   = $files;
      }

      public static function capture(): self
      {
            $realMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

            $uri = $_SERVER['REQUEST_URI'] ?? '/';
            $path = parse_url($uri, PHP_URL_PATH) ?: '/';

            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $base = str_replace('\\', '/', dirname($scriptName));
            $base = rtrim($base, '/');

            if ($base !== '' && str_ends_with($base, '/public')) {
                  $base = substr($base, 0, -strlen('/public'));
                  $base = rtrim($base, '/');
            }

            if ($base !== '' && $base !== '/' && str_starts_with($path, $base)) {
                  $path = substr($path, strlen($base));
                  if ($path === '' || $path === false) {
                        $path = '/';
                  }
            }

            $headers = [];
            foreach ($_SERVER as $key => $value) {
                  if (str_starts_with($key, 'HTTP_')) {
                        $name = str_replace('_', '-', strtolower(substr($key, 5)));
                        $headers[$name] = $value;
                  }
            }

            $query = $_GET ?? [];
            $body  = $_POST ?? [];
            $files = self::normalizeUploadedFiles($_FILES ?? []);

            $contentType = strtolower($_SERVER['CONTENT_TYPE'] ?? '');
            $raw = file_get_contents('php://input') ?: '';

            // JSON para cualquier método
            if (str_contains($contentType, 'application/json')) {
                  $decoded = json_decode($raw, true);
                  if (is_array($decoded)) {
                        $body = $decoded;
                  }
            }
            // Form-urlencoded para PUT/PATCH/DELETE
            elseif (
                  in_array($realMethod, ['PUT', 'PATCH', 'DELETE'], true)
                  && str_contains($contentType, 'application/x-www-form-urlencoded')
            ) {
                  parse_str($raw, $parsedBody);
                  if (is_array($parsedBody)) {
                        $body = $parsedBody;
                  }
            }

            // Method spoofing por _method solo cuando la request real es POST
            $method = $realMethod;
            if ($realMethod === 'POST' && isset($body['_method']) && is_scalar($body['_method'])) {
                  $spoofed = strtoupper(trim((string) $body['_method']));
                  if (in_array($spoofed, ['PUT', 'PATCH', 'DELETE'], true)) {
                        $method = $spoofed;
                        unset($body['_method']);
                  }
            }

            $query = self::normalizeInput($query);
            $body = self::normalizeInput($body);

            return new self($method, $path, $query, $body, $headers, $files);
      }

      public function method(): string
      {
            return $this->method;
      }

      public function isMethod(string $method): bool
      {
            return $this->method === strtoupper($method);
      }

      public function path(): string
      {
            return $this->path;
      }

      public function header(string $name, mixed $default = null): mixed
      {
            $name = strtolower($name);
            return $this->headers[$name] ?? $default;
      }

      public function query(string $key, mixed $default = null): mixed
      {
            return $this->query[$key] ?? $default;
      }

      public function queryAll(): array
      {
            return $this->query;
      }

      public function input(string $key, mixed $default = null): mixed
      {
            $segments = explode('.', $key);
            $value = $this->body;

            foreach ($segments as $segment) {
                  if (!is_array($value) || !array_key_exists($segment, $value)) {
                        return $default;
                  }

                  $value = $value[$segment];
            }

            return $value;
      }

      public function body(): array
      {
            return $this->body;
      }

      public function only(array $keys): array
      {
            $out = [];

            foreach ($keys as $key) {
                  if (array_key_exists($key, $this->body)) {
                        $out[$key] = $this->body[$key];
                  }
            }

            return $out;
      }

      public function string(string $key, string $default = '', bool $trim = true): string
      {
            $value = $this->input($key, $default);
            $value = is_scalar($value) ? (string)$value : $default;

            return $trim ? trim($value) : $value;
      }

      public function int(string $key, int $default = 0): int
      {
            $value = $this->input($key, $default);
            return is_numeric($value) ? (int)$value : $default;
      }

      public function bool(string $key, bool $default = false): bool
      {
            $value = $this->input($key, $default);

            if (is_bool($value)) {
                  return $value;
            }

            if (is_numeric($value)) {
                  return (bool)$value;
            }

            if (is_string($value)) {
                  $normalized = strtolower(trim($value));
                  return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
            }

            return $default;
      }

      public function replace(array $body): void
      {
            $this->body = $body;
      }

      public function file(string $key, mixed $default = null): mixed
      {
            return $this->files[$key] ?? $default;
      }

      public function files(): array
      {
            return $this->files;
      }

      public function hasFile(string $key): bool
      {
            if (!array_key_exists($key, $this->files)) {
                  return false;
            }

            $file = $this->files[$key];

            if (self::isSingleNormalizedFile($file)) {
                  return ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
            }

            if (is_array($file)) {
                  return $file !== [];
            }

            return false;
      }

      private static function normalizeUploadedFiles(array $files): array
      {
            $normalized = [];

            foreach ($files as $key => $file) {
                  if (!is_array($file) || !self::isRawFileField($file)) {
                        $normalized[$key] = $file;
                        continue;
                  }

                  $normalizedFile = self::normalizeFileField($file);
                  $normalized[$key] = self::removeEmptyFiles($normalizedFile);
            }

            return $normalized;
      }

      private static function isRawFileField(array $file): bool
      {
            return isset(
                  $file['name'],
                  $file['type'],
                  $file['tmp_name'],
                  $file['error'],
                  $file['size']
            );
      }

      private static function isSingleNormalizedFile(mixed $value): bool
      {
            return is_array($value)
                  && array_key_exists('name', $value)
                  && array_key_exists('type', $value)
                  && array_key_exists('tmp_name', $value)
                  && array_key_exists('error', $value)
                  && array_key_exists('size', $value);
      }

      private static function normalizeFileField(array $file): array
      {
            if (!is_array($file['name'])) {
                  return [
                        'name' => $file['name'],
                        'type' => $file['type'],
                        'tmp_name' => $file['tmp_name'],
                        'error' => $file['error'],
                        'size' => $file['size'],
                  ];
            }

            return self::normalizeNestedFileField(
                  $file['name'],
                  $file['type'],
                  $file['tmp_name'],
                  $file['error'],
                  $file['size']
            );
      }

      private static function normalizeNestedFileField(
            mixed $names,
            mixed $types,
            mixed $tmpNames,
            mixed $errors,
            mixed $sizes
      ): array {
            $normalized = [];

            foreach ($names as $key => $name) {
                  if (is_array($name)) {
                        $normalized[$key] = self::normalizeNestedFileField(
                              $name,
                              $types[$key] ?? [],
                              $tmpNames[$key] ?? [],
                              $errors[$key] ?? [],
                              $sizes[$key] ?? []
                        );
                        continue;
                  }

                  $normalized[$key] = [
                        'name' => $name,
                        'type' => $types[$key] ?? null,
                        'tmp_name' => $tmpNames[$key] ?? null,
                        'error' => $errors[$key] ?? UPLOAD_ERR_NO_FILE,
                        'size' => $sizes[$key] ?? 0,
                  ];
            }

            return $normalized;
      }

      private static function removeEmptyFiles(mixed $value): mixed
      {
            if (self::isSingleNormalizedFile($value)) {
                  return $value;
            }

            if (!is_array($value)) {
                  return $value;
            }

            $filtered = [];

            foreach ($value as $key => $item) {
                  $item = self::removeEmptyFiles($item);

                  if (self::isSingleNormalizedFile($item)) {
                        $error = $item['error'] ?? UPLOAD_ERR_NO_FILE;

                        // En arreglos múltiples se descartan inputs vacíos
                        if ($error === UPLOAD_ERR_NO_FILE) {
                              continue;
                        }
                  }

                  if (is_array($item) && !self::isSingleNormalizedFile($item) && $item === []) {
                        continue;
                  }

                  $filtered[$key] = $item;
            }

            return $filtered;
      }

      private static function normalizeInput(array $data): array
      {
            foreach ($data as $key => $value) {
                  if (is_array($value)) {
                        $data[$key] = self::normalizeInput($value);
                        continue;
                  }

                  if (!is_string($value)) {
                        continue;
                  }

                  $value = trim($value);
                  $value = str_replace(["\r\n", "\r"], "\n", $value);

                  $data[$key] = $value === '' ? null : $value;
            }

            return $data;
      }
}
