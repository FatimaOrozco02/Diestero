<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Core\Service;
use Core\Session;

final class AuthService extends Service
{
      /** Intenta autenticar a un usuario con su nombre de usuario y contraseña */
      public function attempt(string $username, string $password): array
      {
            $userModel = new User();
            try {
                  // Obtenemos el usuario por su nombre de usuario
                  $user = $userModel->findGeneral(['id', 'username', 'password_hash', 'is_active'], ['username' => $username, 'deleted_at' => null]);
                  if (!$user) {
                        throw new \Exception('Usuario no registrado', 404);
                  }

                  if (!$user['is_active']) {
                        throw new \Exception('Usuario suspendido, contacte a su administrador', 403);
                  }

                  // Verificamos la contraseña
                  if (!password_verify($password, $user['password_hash'])) {
                        throw new \Exception('Credenciales inválidas', 401);
                  }

                  // Guardamos la información del usuario y su organización actual en la sesión
                  Session::set('user', [
                        'id' => (int) $user['id'],
                        'username' => $user['username'],
                        'role_id' => (int) $user['role_id'],
                  ]);

                  return $this->serviceResponse(true, 'Inicio de sesión exitoso', null, 200);
            } catch (\Exception $e) {
                  Session::remove('user');

                  return $this->serviceResponse(false, $e->getMessage(), null, (int) $e->getCode() ?: 500);
            }
      }
}
