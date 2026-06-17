
<?php
declare(strict_types=1);

namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

    $nameP = $_POST['name'];
    $emailP = $_POST['email'];
    $subjectP = $_POST['subject'];
    $messageP = $_POST['message'];
    

    // Validación del correo
    if (empty($emailP)) {
        die("Error: El correo electrónico es obligatorio.");
    }

    if (!filter_var($emailP, FILTER_VALIDATE_EMAIL)) {
        die("Error: El formato del correo electrónico no es válido.");
    }

    // validación del asunto y mensaje
    if (empty($subjectP)) {
        die("Error: El asunto no puede ir vacío.");
    }

    if (empty($messageP)) {
        die("Error: El mensaje no puede ir vacío.");
    }

        //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        // $mail->Username   = 'diestrocorporativo@diestro.com';
        // $mail->Password   = 'D$180886342843ap';                     //SMTP password
        $mail->Username   = 'bibliotechnia2022@gmail.com';
        $mail->Password   = 'escufvjrqoxwnfim';    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($emailP, $nameP);
        
        $mail->addAddress('forozco@difusion.com.mx', 'Diestro');  //Add a recipient


        //Content
        $mail->isHTML(true);                                        //Set email format to HTML
        $mail->Subject = $subjectP;
        $mail->Body    = '<h2>Nuevo mensaje desde el formulario de contacto</h2>
        <p><strong>Nombre:</strong> {$nameP}</p>
        <p><strong>Correo:</strong> {$emailP}</p>
        <p><strong>Asunto:</strong> {$subjectP}</p>
        <p><strong>Mensaje:</strong></p>
        <p>" . nl2br(htmlspecialchars($messageP)) . "</p>';

        $mail->send();
        echo 'El mensaje se ha enviado correctamente';
        
    } catch (Exception $e) {
        echo "El mensaje no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}";
    }
?>    



