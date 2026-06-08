<?php

declare(strict_types=1);

namespace Core;

use Core\View;

abstract class Controller
{
   protected Request $request;
   protected Response $response;
   protected ?View $view = null;

   public function __construct(Request $request, Response $response)
   {
      $this->request = $request;
      $this->response = $response;
   }

   protected function view(): View
   {
      if ($this->view === null) {
         $this->view = new View();
      }
      return $this->view;
   }

   /**
    * Renderiza una plantilla + vista basado en el controlador/metodo
    */
   protected function render(?string $viewFile = null, array $data = [], bool $autoAssets = true, string $layout = 'layouts/main', int $code = 200): void
   {
      if (!$viewFile) {
         $controller = str_replace('Controller', '', (new \ReflectionClass($this))->getShortName());
         $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
         $method = $trace[1]['function'] ?? 'index';
         $viewFile = $controller . '/' . $method;
      }

      $viewFile = $this->normalizeViewFile($viewFile);

      if ($autoAssets) {
         $this->view()->registerAutoAssets($viewFile);
      }

      $html = $this->view->render($viewFile, $data, $layout);
      $this->response->html($html, $code);
   }


   /**
    * Regresa la vista volviendo el camelCase a snake_case
    */
   protected function normalizeViewFile(string $viewFile): string
   {
      // Separar por directorios
      $segments = explode('/', trim($viewFile, '/'));

      $normalized = array_map(function ($segment) {
         return $this->toSnakeCase($segment);
      }, $segments);

      return implode('/', $normalized);
   }

   protected function toSnakeCase(string $value): string
   {
      // Normaliza separadores comunes a underscore
      $value = str_replace(['-', ' '], '_', $value);

      // Maneja límites entre acrónimos y palabras: "APIUser" => "API_User"
      $value = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1_$2', $value);

      // Maneja minúscula/número seguido de mayúscula: "updateUser2FA" => "update_User2_FA"
      $value = preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $value);

      // Todo a minúsculas
      $value = strtolower($value);

      // Colapsa underscores repetidos y limpia bordes
      $value = preg_replace('/_+/', '_', $value);
      $value = trim($value, '_');

      return $value;
   }
}
