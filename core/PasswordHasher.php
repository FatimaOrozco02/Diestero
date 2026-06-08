<?php

declare(strict_types=1);

namespace Core;

final class PasswordHasher
{
     /** Encriptar contraseña */
     public function hash(string $password): string
     {
          return password_hash($password, PASSWORD_DEFAULT);
     }

     /** Comparar contraseña con encriptación */
     public function verify(string $password, string $hash): bool
     {
          return password_verify($password, $hash);
     }

     /** Verifica si el hash concuerda con la codificación actual */
     public function needsRehash(string $hash): bool
     {
          return password_needs_rehash($hash, PASSWORD_DEFAULT);
     }
}
