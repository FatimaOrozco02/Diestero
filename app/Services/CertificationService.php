<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certification;
use App\Models\Certify;
use App\Utils;
use Core\Database;
use Core\Service;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF_FONTS;

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
                  // 'address' => $data['address'] ?? null,
                  // 'content' => $data['content'] ?? null,
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

                  $storagePath = __DIR__ . '/../../storage/';
                  $templatePdf = $storagePath . 'templates/certifies/' . $certify['template'];
                  $certificationPdf = $storagePath . 'uploads/certifications/' . $certificationCode . '.pdf';

                  $fontPath = $storagePath . 'fonts/';

                  $robotoRegular = TCPDF_FONTS::addTTFfont($fontPath . 'Roboto-Regular.ttf', 'TrueTypeUnicode', '', 96);
                  $robotoBold = TCPDF_FONTS::addTTFfont($fontPath . 'Roboto-Bold.ttf', 'TrueTypeUnicode', '', 96);
                  $montserratRegular = TCPDF_FONTS::addTTFfont($fontPath . 'Montserrat-Regular.ttf', 'TrueTypeUnicode', '', 96);
                  $montserratBold = TCPDF_FONTS::addTTFfont($fontPath . 'Montserrat-Bold.ttf', 'TrueTypeUnicode', '', 96);

                  if ($isUpdate && is_file($certificationPdf)) {
                        unlink($certificationPdf);
                  }

                  $pdf = new Fpdi('P', 'mm', 'LETTER', true, 'UTF-8', false);

                  $pdf->setPrintHeader(false);
                  $pdf->setPrintFooter(false);
                  $pdf->SetMargins(0, 0, 0, true);
                  $pdf->SetAutoPageBreak(false, 0);

                  $pdf->setSourceFile($templatePdf);
                  $tplId = $pdf->importPage(1);
                  $size = $pdf->getTemplateSize($tplId);

                  $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                  $pdf->useTemplate($tplId);

                  $pageWidth = $size['width'];
                  $pageHeight = $size['height'];

                  $primaryColor = [23, 57, 97];
                  $accentColor = [140, 61, 70];
                  $textColor = [90, 90, 90];

                  $writeCentered = function (
                        Fpdi $pdf,
                        string $text,
                        float $x,
                        float $y,
                        float $w,
                        float $h,
                        string $font,
                        float $size,
                        array $color,
                        string $style = ''
                  ) {
                        $pdf->SetFont($font, $style, $size);
                        $pdf->SetTextColor($color[0], $color[1], $color[2]);
                        $pdf->SetXY($x, $y);
                        $pdf->MultiCell($w, $h, $text, 0, 'C', false, 1);
                  };

                  $writeLeft = function (
                        Fpdi $pdf,
                        string $text,
                        float $x,
                        float $y,
                        float $w,
                        float $h,
                        string $font,
                        float $size,
                        array $color,
                        string $style = ''
                  ) {
                        $pdf->SetFont($font, $style, $size);
                        $pdf->SetTextColor($color[0], $color[1], $color[2]);
                        $pdf->SetXY($x, $y);
                        $pdf->MultiCell($w, $h, $text, 0, 'L', false, 1);
                  };

                  // Nombre de institución
                  $writeCentered(
                        $pdf,
                        $certificationData['institution'],
                        23,
                        101,
                        170,
                        18,
                        $montserratBold,
                        27,
                        $accentColor
                  );

                  // Número de registro
                  $writeLeft(
                        $pdf,
                        $isUpdate ? $certification['code'] : $certificationData['code'],
                        37,
                        193.5,
                        38,
                        6,
                        $robotoRegular,
                        9,
                        $textColor
                  );

                  // Fecha de emisión
                  $writeLeft(
                        $pdf,
                        $certificationData['start_date'],
                        98,
                        193.5,
                        34,
                        6,
                        $robotoRegular,
                        9,
                        $textColor
                  );

                  // Fecha de vencimiento
                  $writeLeft(
                        $pdf,
                        $certificationData['end_date'],
                        151,
                        193.5,
                        34,
                        6,
                        $robotoRegular,
                        9,
                        $textColor
                  );

                  // Firma
                  if (!empty($signature)) {
                        $signaturePath = realpath($storagePath . 'uploads/signatures/' . $signature);

                        if ($signaturePath !== false && is_readable($signaturePath)) {
                              $imageInfo = getimagesize($signaturePath);

                              if ($imageInfo !== false) {
                                    $imageType = match ($imageInfo[2]) {
                                          IMAGETYPE_PNG => 'PNG',
                                          IMAGETYPE_JPEG => 'JPG',
                                          default => null,
                                    };

                                    if ($imageType !== null) {
                                          $pdf->Image($signaturePath, 94, 220, 28, 0, $imageType);
                                    }
                              }
                        }
                  }

                  // Nombre del certificador
                  $writeCentered(
                        $pdf,
                        $certificationData['certifier'],
                        72,
                        243,
                        72,
                        6,
                        $robotoRegular,
                        11,
                        $textColor
                  );

                  $pdf->Output($certificationPdf, 'F');

                  Database::commit();

                  return $this->serviceResponse(true, "Certificación " . ($isUpdate ? "actualizada" : "creada") . " correctamente", null, $isUpdate ? 200 : 201);
            } catch (\Exception $e) {
                  Database::rollBack();
                  return $this->serviceResponse(false, 'Error al guardar la certificación: ' . $e->getMessage(), null, 500);
            }
      }
}
