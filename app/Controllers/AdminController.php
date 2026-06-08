<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use Core\Config;
use Core\Controller;
use Core\Validator;
use Core\Session;

final class AdminController extends Controller
{
      /** Vista - Muestra el formulario de inicio de sesión */
      public function index(): void
      {
            $this->view()->addLibScript('jquery-validation/jquery.validate.min.js');
            $this->view()->addStyle('admin/styles.css');
            $this->view()->addScript('admin/scripts.js');
            $this->render(null, ['noHeader' => true]);
      }

      /** Vista - Muestra el datatable de certificaciones */
      public function certifications(): void
      {
            $this->view()->addLibStyle('datatables/datatables.min.css');
            $this->view()->addLibScript('datatables/datatables.min.js');
            $this->view()->addLibScript('jquery-validation/jquery.validate.min.js');
            $this->view()->addStyle('admin/styles.css');
            $this->view()->addScript('admin/scripts.js');
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
            $this->response->redirect(Config::get('app.url') . "/admin");
            // $this->response->successJson('Sesión cerrada correctamente', null, 200);
      }
}
