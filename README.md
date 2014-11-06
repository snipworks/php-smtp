PHP SMTP
========

An easy to use SMTP (Simple Mail Transfer Protocol) application which helps you 
to send emails.

## Examples
### Unsecured
```php
<?php

require_once(__DIR__.'/email.php');

$mail = new Email('smtp.example.com', 25);

$mail->setLogin('sender@example.com', 'password');
$mail->addTo('recipient@example.com', 'Example Receiver');
$mail->setFrom('example@example.com', 'Example Sender');
$mail->setSubject('Example subject');
$mail->setMessage('<b>Example message</b>...', true);

if($mail->send()){
    echo 'Succes!';
} else {
    echo 'An error occurred.';
}

```

### Secured (TLS)
```php
<?php

require_once(__DIR__.'/email.php');

$mail = new Email('smtp.example.com', 587);

$mail->setProtocol(Email::TLS);
$mail->setLogin('sender@example.com', 'password');
$mail->addTo('recipient@example.com', 'Example Receiver');
$mail->setFrom('example@example.com', 'Example Sender');
$mail->setSubject('Example subject');
$mail->setMessage('<b>Example message</b>...', true);

if($mail->send()){
    echo 'Succes!';
} else {
    echo 'An error occurred.';
}

```
