# PHP library for sending email with attachments over SMTP

Simple sending of emails via socket. Convenient to use on shared hosting, where there are problems with using PHP mail()
function.

## Install

```bash
composer require onviser/mailer
```

## How to use

Sending simple email:

```php
$mail = (new Mail())
            ->setSubject('This is a subject')
            ->setBody('This is a body')
            ->setFrom('from@example.com')
            ->to('to@example.com');
            
$socket = new Socket('localhost', 25, 'login', 'password');            
$mailer = new SendToSocketMailer($socket);
if ($mailer->send($mail)) {
    // mail was sent    
} else { // error
    echo "error: {$mailer->getError()}" . PHP_EOL;
    print_r($mailer->getLog());
}
```

Email with copy and blind copy:

```php
$mail = (new Mail())
            ->setSubject('This is a subject')
            ->setBody('This is a body')
            ->setFrom('from@example.com')
            ->to('to1@example.com')
            ->to('to2@example.com')
            ->copy('copy1@example.com')
            ->copy('copy2@example.com')
            ->blindCopy('blind-copy@example.com');
```

Email with "Reply-To" header:

```php
$mail = (new Mail())
            ->setSubject('This is a subject')
            ->setBody('This is a body')
            ->setFrom('from@example.com')
            ->setReplayTo('replay-to@example.com')
            ->to('to@example.com');
```

Email with attachments:

```php
$attachmentText = (new Attachment())
                    ->setFileName('file.txt')
                    ->setFileType('text/plain')
                    ->setData('This is a text.');
$attachmentHTML = (new Attachment())
                    ->setFileName('file.html')
                    ->setFileType('text/html')
                    ->setData('<p>This is a <strong>HTML</strong>.</p>');
$mail = (new Mail())
            ->setType(Mail::TYPE_HTML)
            ->setSubject('This is a subject')
            ->setBody('<p>This is a <strong>HTML</strong> body</p>')
            ->setFrom('from@example.com')
            ->to('to@example.com')
            ->attachment($attachmentText)
            ->attachment($attachmentHTML);
```

### Send to file (for development environment)

During development, it is convenient to use storing emails on disk instead of sending them.

```php
$mail = (new Mail())
            ->setSubject('This is a subject')
            ->setBody('This is a body')
            ->setFrom('from@example.com')
            ->to('to@example.com');
            
$mailer = new SendToFileMailer('../var/mail/');
if ($mailer->send($mail)) {
    // mail was saved    
} else { // error
    echo "error: {$mailer->getError()}" . PHP_EOL;
}
```

## Tests

```
./vendor/bin/phpunit
./vendor/bin/phpstan
```

# PHP-библиотека для отправки электронной почты с вложениями по SMTP

Простая отправка электронных писем через сокет. Удобна для использования на виртуальном хостинге, где есть проблемы с
использованием функции PHP mail().