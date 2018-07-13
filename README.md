PHP SMTP
========

An easy to use SMTP (Simple Mail Transfer Protocol) library which helps you to send emails.

## Installation
```bash
composer require snipworks/php-smtp
```

## Examples
### Unsecured
```php
<?php

use Snipworks\Smtp\Email;

$mail = new Email('smtp.example.com', 25);
$mail->setLogin('sender@example.com', 'password');
$mail->addTo('recipient@example.com', 'Example Receiver');
$mail->setFrom('example@example.com', 'Example Sender');
$mail->setSubject('Example subject');
$mail->setHtmlMessage('<b>Example message</b>...');

if($mail->send()){
    echo 'Success!';
} else {
    echo 'An error occurred.';
}

```

### Secured (TLS)
```php
<?php

use Snipworks\Smtp\Email;

$mail = new Email('smtp.example.com', 587);
$mail->setProtocol(Email::TLS);
$mail->setLogin('sender@example.com', 'password');
$mail->addTo('recipient@example.com', 'Example Receiver');
$mail->setFrom('example@example.com', 'Example Sender');
$mail->setSubject('Example subject');
$mail->setHtmlMessage('<b>Example message</b>...');

if($mail->send()){
    echo 'Success!';
} else {
    echo 'An error occurred.';
}

```
It's discouraged to hard-code the SMTP login credentials like in the examples above.
It's recommended to put them inside another file and load it or set it to environment variable 
```php
<?php

// config.php

define('SMTP_PRIMARY_EMAIL', 'sender@example.com');
define('SMTP_PRIMARY_PASSWORD', 'my very secret password');
```

```php
<?php

require_once('config.php');
// ...
$mail->setLogin(SMTP_PRIMARY_EMAIL, SMTP_PRIMARY_PASSWORD);
// ...
```


It's also recommended to put the config outside the public web root if possible. 
This for example prevents people from including your PHP file remotely by a misconfiguration.
