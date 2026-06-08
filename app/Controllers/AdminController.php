<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use Core\Controller;
use Core\Validator;
use Core\Session;

final class AuthController extends Controller
{
      /** Vista - Muestra el formulario de inicio de sesión */
      public function index(): void
      {
            $this->render();
      }

      /** Acción - Inicia la sesión de un usuario */
      public function login(): void
      {
            $validator = Validator::make($this->request->body(), [
                  'username' => 'required|min:3|max:80',
                  'password' => 'required|min:3|max:80',
            ]);

            if ($validator->fails()) {
                  $this->response->errorJson('No se cumplen los requisitos', $validator->errors(), 422);
                  return;
            }

            $data = $validator->validated();

            // Intenta logear un usuario
            $result = (new AuthService)->attempt($data['username'], $data['password']);
            if (!$result['success']) {
                  $this->response->errorJson($result['message'], null, $result['status']);
                  return;
            }

            $this->response->successJson($result['message'], null, 200);
      }

      /** Acción - Cierra la sesión de un usuario */
      public function logout(): void
      {
            Session::destroy();
            $this->response->successJson('Sesión cerrada correctamente', null, 200);
      }
}
