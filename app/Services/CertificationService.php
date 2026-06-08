<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certification;
use App\Models\Certify;
use App\Utils;
use Core\Database;
use Core\Service;
use setasign\Fpdi\Tcpdf\Fpdi;

final class CertificationService extends Service
{
      /** Crea o actualiza una certificación */
      public function createOrUpdate(array $data): array
      {
            $certificationModel = new Certification();
            $isUpdate = !empty($data['id']);

            $certification = null;
            $certificationId = null;
            $certificationCode = null;
            $certifyId = null;
            $signature = null;

            // Calculos de tiempo
            $tz = new \DateTimeZone('America/Mexico_City');
            $today = new \DateTimeImmutable('today', $tz);

            $certificationData = [
                  'institution' => $data['institution'] ?? null,
                  'address' => $data['address'] ?? null,
                  'content' => $data['content'] ?? null,
                  'certifier' => $data['certifier'] ?? null,
            ];

            // Calculo para día de inicio
            $startDateObject = null;
            if (!empty($data['start_date'])) {
                  $startDateObject = new \DateTimeImmutable($data['start_date'], $tz);
            } else {
                  $days = rand(15, 150);
                  $startDateObject =  $today->sub(new \DateInterval("P{$days}D"));
            }
            $certificationData['start_date'] = $startDateObject->format('Y-m-d');

            // Calculo para día de fin
            $endDateObject = null;
            $minEnd = $startDateObject->add(new \DateInterval('P1Y'));
            if (!empty($data['end_date'])) {
                  $endDateObject = new \DateTimeImmutable($data['end_date'], $tz);

                  if ($endDateObject >= $minEnd) {
                        $certificationData['end_date'] = $endDateObject->format('Y-m-d');
                  } else {
                        // No cumple mínimo 1 año → usa +3 años desde start
                        $certificationData['end_date'] = $startDateObject->add(new \DateInterval('P3Y'))->format('Y-m-d');
                  }
            } else {
                  // Sin end_date → +3 años desde start
                  $certificationData['end_date'] = $startDateObject->add(new \DateInterval('P3Y'))->format('Y-m-d');
            }

            // Validar si es actualización o creación
            if ($isUpdate) {
                  $certification = $certificationModel->find($data['id']);
                  if (!$certification) {
                        return $this->serviceResponse(false, 'Certificación no encontrada', null, 404);
                  }
                  $certificationData['updated_at'] = date('Y-m-d H:i:s');
                  $certificationId = $data['id'];
                  $certifyId = $certification['certify_id'];
                  $certificationCode = $certification['code'];
                  $signature = $certification['signature'];
            } else {
                  $certificationData['certify_id'] = $data['certify_id'];
                  $certifyId = $data['certify_id'];

                  // Generación de código
                  $certificationNumber = $certificationModel->count(['certify_id' => $certifyId]) + 1;
                  $certificationCode = $certificationData['code'] = "DIESTRO_" . Utils::randomString(2) . $startDateObject->format('ym')  . $certificationNumber . Utils::randomString(2);
            }

            $certify = (new Certify())->findGeneral(['id', 'shortname', 'template'], ['id' => $certifyId, 'is_active' => true]);
            if (!$certify) {
                  return $this->serviceResponse(false, 'Certificado no encontrado', null, 404);
            }

            Database::beginTransaction();

            try {
                  if ($isUpdate) {
                        // Actualizar
                        $updated = $certificationModel->update($data['id'], $certificationData);
                        if (!$updated) {
                              throw new \Exception('Error al actualizar la certificación', 500);
                        }
                  } else {
                        // Crear
                        $certificationId = $certificationModel->create($certificationData);
                        if (!$certificationId) {
                              throw new \Exception('Error al crear la certificación', 500);
                        }
                  }

                  // Carga de firma
                  if (!empty($data['signature']) && $data['signature']['error'] === UPLOAD_ERR_OK) {
                        $extension = pathinfo($data['signature']['name'], PATHINFO_EXTENSION);
                        $signature = "s" . $certificationId . "." . $extension;

                        $uploaded = Utils::uploadFile('signatures/', $data['signature'], $signature);
                        if (!$uploaded) {
                              throw new \Exception("Error al intentar cargar la firma.");
                        }

                        if (!$certificationModel->update($certificationId, ['signature' => $signature])) {
                              throw new \Exception('Error al guardar el nombre de la firma en la certificación');
                        }
                  }

                  // Creación de PDF
                  $storagePath = __DIR__ . '/../../storage/';
                  $templatePdf = $storagePath . 'templates/certifies/' . $certify['template'];   // PDF de base
                  $certificationPdf = $storagePath . 'uploads/certifications/' . $certificationCode . '.pdf'; // PDF resultante

                  // Eliminamos el certificado anterior
                  if ($isUpdate && is_file($certificationPdf)) {
                        unlink($certificationPdf);
                  }

                  // Crear instancia FPDI+TCPDF
                  $pdf = new Fpdi();

                  // Importar página del PDF base
                  $pdf->setSourceFile($templatePdf);
                  $tplId = $pdf->importPage(1);
                  $size  = $pdf->getTemplateSize($tplId);

                  $pdf->setPrintHeader(false);
                  $pdf->setPrintFooter(false);
                  $pdf->SetHeaderMargin(0);
                  $pdf->SetFooterMargin(0);

                  $pdf->SetAutoPageBreak(false, 0);
                  $pdf->SetMargins(0, 0, 0, true);

                  $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                  $pdf->useTemplate($tplId);
                  $pageWidth = $size['width'];
                  $pageHeight = $size['height'];

                  $formatter = new \IntlDateFormatter(
                        'es_ES',                  // idioma/región
                        \IntlDateFormatter::LONG, // formato largo (ej: "1 de febrero de 2024")
                        \IntlDateFormatter::NONE,
                        'UTC',                    // zona horaria
                        \IntlDateFormatter::GREGORIAN
                  );

                  $pdf->SetTextColor(40, 40, 40); // negro

                  // Institución
                  $pdf->SetFont('helvetica', 'B', 30);
                  $pdf->SetXY(35, 62);
                  $pdf->MultiCell(210, 0, $certificationData['institution'], 0, 'C');

                  // Código
                  $pdf->SetFont('helvetica', '', 10);
                  $pdf->SetXY(110, 111.5);
                  $pdf->Cell(100, 0,  $isUpdate ? $certification['code'] : $certificationData['code'], 0, 1, 'L');

                  // Dirección
                  $pdf->SetFont('helvetica', '', 10);
                  $pdf->SetXY(110, 118.5);
                  $pdf->MultiCell(130, 0, $certificationData['address'], 0, 'L', false);

                  // Contenido
                  $pdf->setCellHeightRatio(1.0); // compacta todo
                  $pdf->SetFont('helvetica', '', 12);
                  $pdf->writeHTMLCell(
                        200, // ancho
                        0,   // alto (0 = autoajustable)
                        45,  // X
                        130,  // Y
                        $certificationData['content'],
                        0,   // borde
                        1,   // salto de línea
                        0,   // fill
                        true, // reset height
                        'J',  // align
                        true // autopadding
                  );

                  // Fecha de inicio
                  $pdf->SetFont('helvetica', 'B', 10);
                  $pdf->SetXY(70, 165);
                  $pdf->Cell(0, 0, $formatter->format($certificationData['start_date']), 0, 1, '');

                  // Fecha de fin
                  $pdf->SetXY(75, 170);
                  $pdf->Cell(0, 0, $formatter->format($certificationData['end_date']), 0, 1, '');

                  // Certificador
                  $pdf->SetFont('helvetica', 'B', 13);
                  $pdf->SetXY($pageWidth - 85, 185);
                  $pdf->Cell(60, 0, $certificationData['certifier'], 0, 1, 'C');

                  // Firma
                  if (!empty($signature)) {
                        $pdf->Image($storagePath . "uploads/signatures/" . $signature, $pageWidth - 70, 160, 30, 0, 'PNG');
                  }

                  // Guardar PDF
                  $pdf->Output($certificationPdf, 'F');

                  Database::commit();

                  return $this->serviceResponse(true, "Certificación " . ($isUpdate ? "actualizada" : "creada") . " correctamente", null, $isUpdate ? 200 : 201);
            } catch (\Exception $e) {
                  Database::rollBack();
                  return $this->serviceResponse(false, 'Error al guardar la certificación: ' . $e->getMessage(), null, 500);
            }
      }
}
