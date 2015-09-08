PHP SMTP
========

An easy to use SMTP (Simple Mail Transfer Protocol) library which helps you 
to send emails.

## Examples
### Unsecured
```php
<?php

use Snipworks\SMTP\Email;
require_once('/path/to/src/email.php');

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

use Snipworks\SMTP\Email;
require_once('/path/to/src/email.php');

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
It's discouraged to hardcode the SMTP login creditials like in the examples above.
It's recommended to put them inside another file and load it. An example would be
like the following.

```php
<?php

// config.php

define('SMTP_PRIMARY_EMAIL', 'sender@example.com');
define('SMTP_PRIMARY_PASSWORD', 'my very secret password');
```

```php
<?php

require_once('config.php')

// ...

$mail->setLogin(SMTP_PRIMARY_EMAIL, SMTP_PRIMARY_PASSWORD);

// ...

```
It's also recommended to put the config outside the public web root if possible. 
This for example prevents people from including your PHP file remotely by a 
misconfiguration.
