<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Certification;
use Core\Controller;

final class MediaController extends Controller
{
      /** Obtiene la firma de una certificación */
      public function certification(int $certificationId): void
      {
            $certification = (new Certification())->findGeneral(['id', 'code'], ['id' => $certificationId, 'deleted_at' => null]);
            if (!$certification) {
                  $this->response->errorJson('No se encontró la certificación', null, 404);
            }

            $this->renderFile('certifications/' . $certification['code'] . '.pdf');
      }

      /** Obtiene la firma de una certificación */
      public function signature(int $certificationId): void
      {
            $certification = (new Certification())->findGeneral(['id', 'signature'], ['id' => $certificationId, 'deleted_at' => null]);
            if (!$certification) {
                  $this->response->errorJson('No se encontró la certificación', null, 404);
            }

            $this->renderFile('signatures/' . $certification['signature']);
      }

      /** Renderiza un archivo desde la carpeta de uploads */
      protected function renderFile(string $path): void
      {
            $uploadsPath = __DIR__ . '/../../storage/uploads/';
            $fullPath = realpath($uploadsPath . $path);

            if (!$uploadsPath || !$fullPath || !is_file($fullPath)) {
                  $this->response->errorJson('Archivo no encontrado', null, 404);
                  return;
            }

            $mime = mime_content_type($fullPath);

            header('Content-Type: ' . $mime);
            header('Content-Length: ' . filesize($fullPath));
            header('Cache-Control: public, max-age=86400');

            readfile($fullPath);
      }
}
