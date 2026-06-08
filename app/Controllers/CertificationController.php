<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Area;
use App\Models\Board;
use App\Models\BoardMember;
use App\Models\BoardVisibility;
use App\Models\Task;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Services\BoardService;
use App\Utils\Message;
use Core\Controller;
use Core\Session;
use Core\Validator;

final class BoardController extends Controller
{
      /** Vista - Lista de tableros */
      public function index(): void
      {
            $data = [
                  'mainMenuOption' => 'boards',
                  'breadcrumb' => [
                        ['label' => 'Tableros', 'url' => null, 'active' => true],
                  ],
                  'visibilities' => (new BoardVisibility())->getGeneral(['id', 'name', 'code'], ['is_active' => true]),
                  'areas' => (new Area())->getActiveRecords()
            ];

            $this->view()->addLibStyle('datatable/datatables.min.css');
            $this->view()->addLibScript('datatable/datatables.min.js');

            $this->render(null, $data);
      }

      /** Vista - Detalle de un tablero */
      public function show(int $boardId): void
      {
            // Obtenemos el tablero
            $board = (new Board())->findWithVisibilityAndArea($boardId);
            if (!$board) {
                  $this->error([
                        'code' => 404,
                        'message' => 'Tablero no encontrado.',
                        'redirect' => 'boards',
                        'redirectMessage' => 'Volver a tableros',
                  ]);
                  return;
            }

            $sessionUser = Session::get('user');
            $userRole = (new BoardMember())->findUserRole($sessionUser['id'], $boardId);
            if (!$userRole) {
                  $this->error([
                        'code' => 403,
                        'message' => 'No tienes permiso para acceder a este tablero.',
                        'redirect' => 'boards',
                        'redirectMessage' => 'Volver a tableros',
                  ]);
                  return;
            }
            $canEdit = in_array($userRole['role_code'], ['owner', 'admin']);

            $data = [
                  'mainMenuOption' => 'boards',
                  'breadcrumb' => [
                        ['label' => 'Tableros', 'url' => 'boards'],
                        ['label' => $board['name'], 'url' => null, 'active' => true],
                  ],
                  'board' => $board,
                  'canEdit' => $canEdit,
                  'taskPriorities' => (new TaskPriority())->getGeneral(['id', 'name', 'color'], ['is_active' => true], 'position ASC'),
                  'taskStatuses' => (new TaskStatus())->getGeneral(['id', 'name', 'color', 'code'], ['is_active' => true], 'position ASC'),
                  'boardMembers' => (new BoardMember())->getByBoard($boardId)
            ];

            $this->view()->addLibScript('select2/select2.full.min.js');
            $this->view()->addLibScript('select2/es.js');
            $this->view()->addLibStyle('select2/select2.min.css');
            $this->view()->addLibStyle('select2/select2-bootstrap-5-theme.min.css');
            $this->render(null, $data);
      }

      /** JSON - Obtiene los datos de la tabla de tableros */
      public function getTableData(): void
      {
            $boards = (new Board())->getTableData();
            $this->response->successJson('listado de tableros', $boards);
      }

      /** JSON - Obtiene los datos de un tablero */
      public function getDetail(int $boardId): void
      {
            $board = (new Board())->findGeneral(['id', 'name', 'description', 'visibility_id', 'area_id'], ['id' => $boardId]);
            if (!$board) {
                  $this->response->errorJson('Tablero no encontrado', null, 404);
                  return;
            }

            $this->response->successJson('Datos del tablero', $board);
      }

      /** JSON - Obtiene el conteo de tareas por estado de un tablero */
      public function getTaskStatusesCounts(int $boardId): void
      {
            $board = (new Board())->findGeneral(['id'], ['id' => $boardId]);
            if (!$board) {
                  $this->response->errorJson('Tablero no encontrado', null, 404);
                  return;
            }

            $data = [
                  'tasks_count' => (new Task())->getCountByBoard($boardId),
                  'statuses_counts' => (new TaskStatus())->getWithTaskCountByBoard($boardId)
            ];
            $this->response->successJson('Conteo de tareas por estado', $data);
      }

      /** JSON - Crea un nuevo tablero */
      public function store(): void
      {
            $validator = Validator::make($this->request->all(), [
                  'name' => 'required|string|min:3|max:255',
                  'description' => 'nullable|string|min:3|max:500',
                  'visibility_id' => 'required|integer',
                  'area_id' => 'nullable|integer',
                  'include_area_members' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                  $this->response->errorJson(Message::REQUIREMENTS, $validator->errors(), 422);
                  return;
            }

            $data = $validator->validated();

            $visibilityExists = (new BoardVisibility())->exists(['id' => $data['visibility_id'], 'is_active' => true]);
            if (!$visibilityExists) {
                  $this->response->errorJson('Visibilidad no válida', null, 422);
                  return;
            }

            $response = (new BoardService())->createOrUpdate($data);
            if ($response['success']) {
                  $this->response->successJson($response['message'], null, $response['status']);
            } else {
                  $this->response->errorJson($response['message'], null, $response['status']);
            }
      }

      /** JSON - Actualiza un tablero */
      public function update(int $boardId): void
      {
            $validator = Validator::make($this->request->all(), [
                  'name' => 'required|string|min:3|max:255',
                  'description' => 'nullable|string|min:3|max:500',
                  'visibility_id' => 'required|integer',
                  'area_id' => 'nullable|integer',
                  'include_area_members' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                  $this->response->errorJson(Message::REQUIREMENTS, $validator->errors(), 422);
                  return;
            }

            $data = $validator->validated();
            $sessionUser = Session::get('user');

            $board = (new Board())->findGeneral(['id', 'created_by'], ['id' => $boardId, 'deleted_at' => null]);
            if (!$board) {
                  $this->response->errorJson('Tablero no encontrado', null, 404);
                  return;
            }

            if ($board['created_by'] !== $sessionUser['id']) {
                  $this->response->errorJson('No tienes permiso para actualizar este tablero', null, 403);
                  return;
            }

            $data['id'] = $board['id'];

            $visibilityExists = (new BoardVisibility())->exists(['id' => $data['visibility_id'], 'is_active' => true]);
            if (!$visibilityExists) {
                  $this->response->errorJson('Visibilidad no válida', null, 422);
                  return;
            }

            $response = (new BoardService())->createOrUpdate($data);
            if ($response['success']) {
                  $this->response->successJson($response['message'], null, $response['status']);
            } else {
                  $this->response->errorJson($response['message'], null, $response['status']);
            }
      }

      /** JSON - Elimina un tablero de forma lógica */
      public function destroy(int $boardId): void
      {
            $board = (new Board())->findGeneral(['id', 'created_by'], ['id' => $boardId, 'deleted_at' => null]);
            if (!$board) {
                  $this->response->errorJson('Tablero no encontrado', null, 404);
                  return;
            }

            $sessionUser = Session::get('user');
            if ($board['created_by'] !== $sessionUser['id']) {
                  $this->response->errorJson('No tienes permiso para eliminar este tablero', null, 403);
                  return;
            }

            try {
                  (new Board())->softDelete($boardId);
                  $this->response->successJson('Tablero eliminado exitosamente');
            } catch (\Exception $e) {
                  $this->response->errorJson('Error al eliminar el tablero: ' . $e->getMessage(), null, 500);
            }
      }
}
