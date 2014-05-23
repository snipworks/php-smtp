<?php

require_once(__DIR__ . '/email.php');
$mail = new Email('smtp.gmail.com', 465);
$mail->setProtocol(Email::SSL); //SSL or TLS can be used. Or if there's other protocol you have 
$mail->setLogin('<email address>', '<password>');
$mail->addTo('<receiver email>', '<receiver name>'); //receiver's name is optional
$mail->setFrom('<sender email>', '<sender name>'); //sender's name is optional
$mail->setSubject('Test subject');
$mail->setMessage('<b>test message</b>', true); //argument 2=true (send HTML mail) | default: false (plain text)

echo (($mail->send()) ? 'Mail has been sent' : 'Error sending email') . PHP_EOL;

print_r($mail->getLog()); //display SMTP log
