<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * 
 */
function send_mail(string $to, string $name, string $subject, string $body): bool
{
    $mail = new PHPMailer(true);

    $mail->SMTPDebug = ENVIRONMENT === 'production' ? SMTP::DEBUG_OFF : SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Port = 465;
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['EMAIL_ADDRESS'];
    $mail->Password = $_ENV['EMAIL_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    $mail->setFrom($_ENV['EMAIL_ADDRESS'], 'Jay Rods');
    $mail->addAddress($to, $name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = $body;

    if (!$mail->send()) {
        return false;
    } else {
        if (save_mail($mail)) {
            echo "Message saved!";
        }

        return true;
    }
}

/**
 * 
 */
function save_mail($mail)
{
    $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';

    $imapStream = imap_open($path, $mail->Username, $mail->Password);

    $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());

    imap_close($imapStream);

    return $result;
}
