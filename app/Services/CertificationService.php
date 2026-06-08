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
            if (!empty($data['start_date'])) {
                  $certificationData['start_date'] = new \DateTimeImmutable($data['start_date']);
            } else {
                  $days = rand(15, 150);
                  $certificationData['start_date'] =  $today->sub(new \DateInterval("P{$days}D"));
            }

            // Calculo para día de fin
            $minEnd = $certificationData['start_date']->add(new \DateInterval('P1Y'));
            if (!empty($data['end_date'])) {
                  $end_date = new \DateTimeImmutable($data['end_date'], $tz);

                  if ($end_date >= $minEnd) {
                        $certificationData['end_date'] = $end_date;
                  } else {
                        // No cumple mínimo 1 año → usa +3 años desde start
                        $certificationData['end_date'] = $certificationData['start_date']->add(new \DateInterval('P3Y'));
                  }
            } else {
                  // Sin end_date → +3 años desde start
                  $certificationData['end_date'] = $certificationData['start_date']->add(new \DateInterval('P3Y'));
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
            } else {
                  $certificationData['certify_id'] = $data['certify_id'];
                  $certifyId = $data['certify_id'];

                  // Generación de código
                  $certificationNumber = $certificationModel->count(['certify_id' => $certifyId]) + 1;
                  $certificationCode = $certificationData['code'] = "DIESTRO_" . Utils::randomString(2) . $certificationData['start_date']->format('ym')  . $certificationNumber . Utils::randomString(2);
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
                  if (!empty($data['signature']) && isset($_FILES['signature']) && $_FILES['signature']['error'] === UPLOAD_ERR_OK) {
                        $signature = "s" . $certificationId . ".png";

                        if (!Utils::uploadFile('signatures/', $_FILES['signature'], $signature)) {
                              throw new \Exception("Error al intentar cargar la firma.");
                        }

                        if (!$certificationModel->update($certificationId, ['signature' => $signature])) {
                              throw new \Exception('Error al guardar el nombre de la firma en la certificación');
                        }
                  }

                  // Creación de PDF
                  $pdfBase = __DIR__ . '/../assets/' . $certify['template'];   // PDF de base
                  $pdfSalida = __DIR__ . '/../assets/uploads/certifications/' . $certificationCode . '.pdf'; // PDF resultante

                  // Eliminamos el certificado anterior
                  if ($isUpdate && is_file($pdfSalida)) {
                        unlink($pdfSalida);
                  }

                  // Crear instancia FPDI+TCPDF
                  $pdf = new Fpdi();

                  // Importar página del PDF base
                  $pdf->setSourceFile($pdfBase);
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

                  // Configuración de fuente
                  $pdf->SetFont('helvetica', 'B', 17);
                  $pdf->SetTextColor(40, 40, 40); // negro
                  $pdf->SetXY(0, 73);
                  $pdf->Cell(0, 10, $isUpdate ? $certification['institution'] : $certificationData['institution'], 0, 1, 'C');

                  $pdf->SetFont('helvetica', '', 10);
                  $pdf->SetTextColor(99, 99, 99); // gris

                  $pdf->SetXY(37, 91);
                  $pdf->MultiCell(135, 6, $isUpdate ? $certification['address'] : $certificationData['address'], 0, 'C', false);

                  $pdf->setCellHeightRatio(1.0); // compacta todo
                  $pdf->SetFont('helvetica', '', 11);
                  $pdf->SetTextColor(40, 40, 40); // negro

                  $contentY = 103;
                  $pdf->writeHTMLCell(
                        190, // ancho
                        0,   // alto (0 = autoajustable)
                        14,  // X
                        $contentY,  // Y
                        $isUpdate ? $certification['content'] : $certificationData['content'],
                        0,   // borde
                        1,   // salto de línea
                        0,   // fill
                        true, // reset height
                        'J',  // align
                        true // autopadding
                  );

                  $formatter = new \IntlDateFormatter(
                        'es_ES',                  // idioma/región
                        \IntlDateFormatter::LONG, // formato largo (ej: "1 de febrero de 2024")
                        \IntlDateFormatter::NONE,
                        'UTC',                    // zona horaria (ajusta si necesitas)
                        \IntlDateFormatter::GREGORIAN
                  );

                  $pdf->SetFont('helvetica', 'B', 10);
                  $pdf->SetTextColor(38, 84, 138); // azul
                  $datesY = 217.5;
                  $pdf->SetXY(81, $datesY);
                  $pdf->Cell(0, 10, $formatter->format($isUpdate ? $certification['start_date'] : $certificationData['start_date']), 0, 1, '');

                  $pdf->SetXY(147, $datesY);
                  $pdf->Cell(0, 10, $formatter->format($isUpdate ? $certification['end_date'] : $certificationData['end_date']), 0, 1, '');

                  $pdf->SetFont('helvetica', 'B', 10);
                  $certifierY = 245;
                  if (in_array($isUpdate ? $certification['certify_id'] : $certificationData['certify_id'], [6])) {
                        $certifierY = 248;
                  }
                  $pdf->SetXY(0, $certifierY);
                  $pdf->Cell(0, 10, $isUpdate ? $certification['certifier'] : $certificationData['certifier'], 0, 1, 'C');

                  $pdf->SetFont('helvetica', 'B', 11);
                  $pdf->SetTextColor(40, 40, 40); // negro
                  $pdf->SetXY(0, 252);
                  $codeAlign = 'C';
                  $pdf->Cell(0, 10,  $isUpdate ? $certification['code'] : $certificationData['code'], 0, 1, $codeAlign);

                  if (!empty($isUpdate ? $certification['signature'] : $certificationData['signature'])) {
                        $pdf->Image("assets/uploads/signatures/" . ($isUpdate ? $certification['signature'] : $certificationData['signature']), (($pageWidth - 30) / 2), 230, 30, 0, 'PNG');
                  }

                  // Guardar PDF
                  $pdf->Output($pdfSalida, 'F');

                  Database::commit();

                  return $this->serviceResponse(true, "Certificación " . ($isUpdate ? "actualizada" : "creada") . " correctamente", null, $isUpdate ? 200 : 201);
            } catch (\Exception $e) {
                  Database::rollBack();
                  return $this->serviceResponse(false, 'Error al guardar la certificación: ' . $e->getMessage(), null, 500);
            }
      }
}
