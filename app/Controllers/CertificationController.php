<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Certification;
use App\Models\Certify;
use App\Services\CertificationService;
use Core\Controller;
use Core\Session;
use Core\Validator;

final class CertificationController extends Controller
{
      /** Vista - Crear nueva certificación */
      public function create(): void
      {
            $data = [
                  'certifies' => (new Certify())->getGeneral(['id', 'shortname'], ['is_active' => true], 'shortname ASC')
            ];

            $this->view()->addLibScript('tinymce/tinymce.min.js');
            $this->view()->addLibScript('jquery-validation/jquery.validate.min.js');
            $this->view()->addStyle('admin/styles.css');
            $this->view()->addScript('admin/scripts.js');
            $this->render('certification/form', $data);
      }

      /** Vista - Actualizar certificación */
      public function show(int $certificationId): void
      {
            $certification = (new Certification())->findGeneral(['id', 'certify_id', 'code', 'institution', 'address', 'content', 'certifier', 'start_date', 'end_date', 'signature', 'is_active'], ['id' => $certificationId, 'deleted_at' => null]);
            if (!$certification) {
                  $this->response->errorJson('Certificación no encontrada', null, 404);
                  return;
            }

            $data = [
                  'certification' => $certification,
                  'certifies' => (new Certify())->getGeneral(['id', 'shortname'], ['is_active' => true], 'shortname ASC')
            ];

            $this->view()->addLibScript('tinymce/tinymce.min.js');
            $this->view()->addLibScript('jquery-validation/jquery.validate.min.js');
            $this->view()->addStyle('admin/styles.css');
            $this->view()->addScript('admin/scripts.js');
            $this->render('certification/form', $data);
      }

      /** JSON - Obtiene los datos de la tabla de certificaciones */
      public function getTableData(): void
      {
            $certifications = (new Certification())->getTableData();
            $this->response->successJson('listado de certificaciones', $certifications);
      }

      /** JSON - Obtiene los datos de una certificación */
      public function getDetail(int $certificationId): void
      {
            $certification = (new Certification())->findActiveWithCertify($certificationId);
            if (!$certification) {
                  $this->response->errorJson('Certificación no encontrada', null, 404);
                  return;
            }

            $this->response->successJson('Datos de la certificación', $certification);
      }

      /** JSON - Crea una nueva certificación */
      public function store(): void
      {
            $validator = Validator::make(array_merge($this->request->body(), $this->request->files()), [
                  'certify_id' => 'required|integer',
                  'institution' => 'required|string|min:3|max:250',
                  'address' => 'required|string|min:3|max:250',
                  'content' => 'required|string|min:3|max:2000',
                  'certifier' => 'required|string|min:3|max:100',
                  'start_date' => 'nullable|date',
                  'end_date' => 'nullable|date',
                  'signature' => 'nullable',
            ]);

            if ($validator->fails()) {
                  $this->response->errorJson('No se cumplen los requisitos', $validator->errors(), 422);
                  return;
            }

            $data = $validator->validated();

            $response = (new CertificationService())->createOrUpdate($data);
            if ($response['success']) {
                  $this->response->successJson($response['message'], null, $response['status']);
            } else {
                  $this->response->errorJson($response['message'], null, $response['status']);
            }
      }

      /** JSON - Actualiza una certificación */
      public function update(int $certificationId): void
      {
            $validator = Validator::make(array_merge($this->request->body(), $this->request->files()), [
                  'institution' => 'required|string|min:3|max:250',
                  'address' => 'required|string|min:3|max:250',
                  'content' => 'required|string|min:3|max:2000',
                  'certifier' => 'required|string|min:3|max:100',
                  'start_date' => 'nullable|date',
                  'end_date' => 'nullable|date',
                  'signature' => 'nullable'
            ]);

            if ($validator->fails()) {
                  $this->response->errorJson('No se cumplen los requisitos', $validator->errors(), 422);
                  return;
            }

            $data = $validator->validated();

            $certification = (new Certification())->findGeneral(['id'], ['id' => $certificationId, 'deleted_at' => null]);
            if (!$certification) {
                  $this->response->errorJson('Certificación no encontrada', null, 404);
                  return;
            }
            $data['id'] = $certification['id'];

            $response = (new CertificationService())->createOrUpdate($data);
            if ($response['success']) {
                  $this->response->successJson($response['message'], null, $response['status']);
            } else {
                  $this->response->errorJson($response['message'], null, $response['status']);
            }
      }

      /** JSON - Elimina un tablero de forma lógica */
      public function destroy(int $certificationId): void
      {
            $certification = (new Certification())->findGeneral(['id'], ['id' => $certificationId, 'deleted_at' => null]);
            if (!$certification) {
                  $this->response->errorJson('Certificación no encontrada', null, 404);
                  return;
            }

            try {
                  (new Certification())->softDelete($certificationId);
                  $this->response->successJson('Certificación eliminada exitosamente');
            } catch (\Exception $e) {
                  $this->response->errorJson('Error al eliminar la certificación: ' . $e->getMessage(), null, 500);
            }
      }
}
