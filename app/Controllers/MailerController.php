<?php

declare(strict_types=1);

namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Core\Controller;

final class MailerController extends Controller {

    public function enviar() {
        // Obliga al servidor a responder en formato JSON limpio
        header('Content-Type: application/json');

        // 1. Recibir y limpiar datos básicos
        $nameP    = trim($_POST['name'] ?? '');
        $emailP   = trim($_POST['email'] ?? '');
        $subjectP = trim($_POST['subject'] ?? '');
        $messageP = trim($_POST['message'] ?? '');

        // Validación del formato de correo electrónico
        if (empty($emailP) || !filter_var($emailP, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'El formato del correo electrónico no es válido.']);
            exit;
        }

        // Validación de campos vacíos obligatorios
        if (empty($subjectP) || empty($messageP)) {
            echo json_encode(['status' => 'error', 'message' => 'Todos los campos obligatorios deben ir llenos.']);
            exit;
        }

        $mail = new PHPMailer(true);

        try {
            // Configuración del Servidor Outlook / Office 365
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // Mantener apagado para no corromper la respuesta JSON                      
            $mail->isSMTP();                                            
            
            $mail->Host       = 'smtp.office365.com';                       
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = 'forozco@difusion.com.mx';
            $mail->Password   = 'Fa19or26@';    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            
            $mail->Port       = 587;  
            
            // $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
            // $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            // $mail->Username   = 'bibliotechnia2022@gmail.com';
            // $mail->Password   = 'escufvjrqoxwnfim';                     //SMTP password
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            // $mail->Port       = 465;



            
            $mail->setFrom($emailP, $nameP);
            $mail->addAddress('forozco@difusion.com.mx', 'Diestro');  

            
            $mail->isHTML(true);                                        
            $mail->Subject = $subjectP;
            $mail->Body    = "
                <h2>Nuevo mensaje desde el formulario de contacto</h2>
                <p><strong>Nombre:</strong> {$nameP}</p>
                <p><strong>Correo:</strong> {$emailP}</p>
                <p><strong>Asunto:</strong> {$subjectP}</p>
                <p><strong>Mensaje:</strong></p>
                <p>" . nl2br(htmlspecialchars($messageP)) . "</p>
            ";

            
            $mail->send();
            
            
            echo json_encode(['status' => 'success', 'message' => 'El mensaje se ha enviado correctamente.']);
            exit;
            
        } catch (Exception $e) {
            
            echo json_encode(['status' => 'error', 'message' => 'El mensaje no pudo ser enviado. Inténtelo más tarde.']);
            // echo "Errror: {$mail->ErrorInfo}";
            exit;
        }
    }
}
