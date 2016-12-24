<?php

require_once "PHPMailer-master/PHPMailerAutoload.php";

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'mx1.hostinger.co.uk';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'itemrequestemail@myelephant.xyz';                 // SMTP username
$mail->Password = 'susapp@1';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to
$mail->SetFrom('itemrequestemail@myelephant.xyz','The elphant app item request.');
$mail->AddReplyTo('nav8699@gmail.com', 'The elephant app user.');     // Add a recipient
$mail->AddAddress('dhutin@lsbu.ac.uk','The elphant app item request.');               // Name is optional

//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'The elephant item request.';
$mail->Body    = 'Hi, Paul<br> The elephant app is sending you this email on behalf og nav for iphone<br><hr>Hey, I am interested in your item.<br><hr>';

// function emailUser() {
//   $to_email = 'dhutin@lsbu.ac.uk';
//   $to_username = 'Nav';
//   $itemname = 'Notepad';
//   $subject = "The elephant app item upload request.";
//   $message = <<<HTML
// <b>This is an automated email sent by the elephant app:</b><hr>
// Dear {$to_username},<br/><br/>
// Your {$itemname} has been declined.<br><br>
// Regards,<br>the elephant app team.<br><br><hr>
// HTML;
//   $header  = "From: no-reply@myelephant.xyz \r\n";
//   $header .= "MIME-Version: 1.0\r\n";
//   $header .= "Content-type: text/html\r\n";
//   return mail($to_email, $subject, $message, $header) == TRUE;
// }
// if(emailUser()) {
//   echo 'Sent.';
// } else {
//   echo 'Error.';
// }
if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
