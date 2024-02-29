<?php declare(strict_types=1);

namespace Onviser\Mailer;

interface MailerInterface
{
    public function send(Mail $mail): bool;

    public function getError(): string;
}