<?php

use Snipworks\Smtp\Email;

require_once(dirname(__DIR__) . '/vendor/autoload.php');

$mail = new Email('smtp.gmail.com', 587);
$mail->setProtocol(Email::TLS)
    ->setLogin('sender@example.com', 'P4ssW0rD!')
    ->setFrom('sender@example.com')
    ->setSubject('Test subject')
    ->setTextMessage('Plain text message')
    ->setHtmlMessage('<strong>HTML Text Message</strong>')
    ->addTo('receiver@example.com')
    ->addAttachment(dirname(__DIR__) . '/LICENSE')
    ->addAttachment(dirname(__DIR__) . '/README.md');

if ($mail->send()) {
    echo 'SMTP Email has been sent' . PHP_EOL;
    exit(0);
}

echo 'An error has occurred. Please check the logs below:' . PHP_EOL;
print_r($mail->getLogs());
