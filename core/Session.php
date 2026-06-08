<?php

declare(strict_types=1);

namespace Core;

final class Session
{
   /*** Obtiene el valor de una variable de sesion */
   public static function get(string $key, $default = null)
   {
      return $_SESSION[$key] ?? $default;
   }

   /*** Asigna un valor a una variable de sesión */
   public static function set(string $key, $value): void
   {
      $_SESSION[$key] = $value;
   }

   /*** Verifica si existe una variable de sesion */
   public static function has(string $key): bool
   {
      return isset($_SESSION[$key]);
   }

   /*** Elimina una variable de sesión */
   public static function remove(string $key): void {
      unset($_SESSION[$key]);
   }

   /* Destruye la sesión completa */
   public static function destroy(): void {
      session_destroy();
   }
}
