<?php

declare(strict_types=1);

namespace Core;

abstract class Service
{
      /** Respuesta estandar de un servicio */
      protected function serviceResponse( bool $success = true, string $message = '', mixed $data = null, int $status = 200): array
      {
            return [
                  'success' => $success,
                  'message' => $message,
                  'data' => $data,
                  'status' => $status,
            ];
      }
}
