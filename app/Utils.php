<?php

declare(strict_types=1);

namespace App;

final class Utils
{
      /** Genera una cadena aleatoria de letras mayúsculas y números */
      public static function randomString(int $length = 8): string
      {
            $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
            $max = strlen($alphabet) - 1;

            $out = '';
            for ($i = 0; $i < $length; $i++) {
                  $out .= $alphabet[random_int(0, $max)];
            }

            return $out;
      }

      public static function uploadFile(string $directory, array $file, ?string $fileName = null, bool $replace = true, ?int $maxSize = null, array $allowedMimeTypes = []): bool
      {
            $baseDir = dirname(__DIR__, 1) . '/storage/uploads/' . trim($directory, '/');
            $targetName = $fileName ?: basename((string)($file['name'] ?? ''));
            $targetPath = $baseDir . '/' . $targetName;

            if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                  return false;
            }

            if ($allowedMimeTypes !== []) {
                  $mime = mime_content_type($file['tmp_name']) ?: '';
                  if (!in_array($mime, $allowedMimeTypes, true)) {
                        return false;
                  }
            }

            if ($maxSize !== null && isset($file['size']) && (int)$file['size'] > $maxSize) {
                  return false;
            }

            if (!$replace && file_exists($targetPath)) {
                  return false;
            }

            if (!is_dir($baseDir)) {
                  mkdir($baseDir, 0777, true);
            }

            $result = move_uploaded_file($file['tmp_name'], $targetPath);

            return $result;
      }

      public static function removeDirectory(string $path): bool
      {
            if (!file_exists($path)) {
                  return false;
            }

            if (is_file($path) || is_link($path)) {
                  return unlink($path);
            }

            foreach (scandir($path) ?: [] as $item) {
                  if ($item === '.' || $item === '..') {
                        continue;
                  }
                  self::removeDirectory($path . DIRECTORY_SEPARATOR . $item);
            }

            return rmdir($path);
      }
}
