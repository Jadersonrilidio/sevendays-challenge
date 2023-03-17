<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * 
 */
function send_mail(string $to, string $name, string $subject, string $body): bool
{
    // PHPMailer config
    $mail = new PHPMailer(true);

    $SMPTDebug = (isset($_ENV['ENVIRONMENT']) and $_ENV['ENVIRONMENT'] === 'production')
        ? SMTP::DEBUG_OFF
        : SMTP::DEBUG_SERVER;

    $mail->SMTPDebug = $SMPTDebug;                              //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Port = 465;                                          //Set SMTP port number: 465 for SMTP with implicit TLS
    $mail->Host       = $_ENV['EMAIL_SMPT_HOST'];               //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $_ENV['EMAIL_ADDRESS'];                 //SMTP username
    $mail->Password   = $_ENV['EMAIL_PASSWORD'];                //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption

    // Sending email
    $mail->setFrom($_ENV['EMAIL_ADDRESS'], 'Jay Rods');
    $mail->addAddress($to, $name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = $body;

    if (!$mail->send()) {
        error_log('Mailer Error: ' . $mail->ErrorInfo);

        return false;
    } else {
        if (save_mail($mail)) {
            echo "Message saved!";
        }

        return true;
    }
}

//Section 2: IMAP
//IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
//Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
//You can use imap_getmailboxes($imapStream, '/imap/ssl', '*' ) to get a list of available folders or labels, this can
//be useful if you are trying to get this working on a non-Gmail IMAP server.
function save_mail($mail)
{
    //You can change 'Sent Mail' to any other folder or tag
    $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';

    //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
    $imapStream = imap_open($path, $mail->Username, $mail->Password);

    $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
    imap_close($imapStream);

    return $result;
}
