<?php

declare(strict_types=1);

use Core\Config;
use Core\Session;

if (!function_exists('e')) {
   function e(mixed $value): string
   {
      // Escape seguro para HTML (XSS)
      return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
   }
}

if (!function_exists('csrf_token')) {
   function csrf_token(): string
   {
      // Generar token si no existe
      if (empty($_SESSION['_csrf_token'])) {
         $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
      }
      return (string)$_SESSION['_csrf_token'];
   }
}

if (!function_exists('csrf_field')) {
   function csrf_field(): string
   {
      // Campo oculto para formularios HTML
      return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
   }
}

if (!function_exists('auth_profile')) {
   function auth_profile(): ?int
   {
      return isset($_SESSION['profile_id'])
         ? (int)$_SESSION['profile_id']
         : null;
   }
}

if (!function_exists('profile_in')) {
   function profile_in(int ...$profiles): bool
   {
      $current = auth_profile();
      return $current !== null && in_array($current, $profiles, true);
   }
}

if (!function_exists('publicUrl')) {
   function publicUrl(string $path): string
   {
      $base = Config::get('app.url', '');
      return rtrim($base, '/') . '/' . ltrim($path, '/');
   }
}

if (!function_exists('app_locale')) {
   function app_locale(): string
   {
      $locale = (string)Session::get('lang', 'es');
      return in_array($locale, ['es', 'en'], true) ? $locale : 'es';
   }
}

if (!function_exists('trans')) {
   function trans(string $key, ?string $locale = null): string
   {
      $lang = $locale ?? app_locale();
      $file = dirname(__DIR__) . '/resources/lang/' . $lang . '.php';

      static $cache = [];

      if (!isset($cache[$lang])) {
         $cache[$lang] = file_exists($file) ? (require $file) : [];
      }

      return $cache[$lang][$key] ?? $key;
   }
}

if (!function_exists('switch_lang')) {
   function switch_lang(string $lang): string
   {
      $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
      $appBase = rtrim((string) Config::get('app.url', ''), '/');

      return $appBase . '/idioma/' . $lang . '?redirect=' . urlencode($currentUri);
   }
}
