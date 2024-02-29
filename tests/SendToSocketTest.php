<?php declare(strict_types=1);

namespace Tests;

use Onviser\Mailer\Mail;
use Onviser\Mailer\MailerInterface;
use Onviser\Mailer\SendToSocketMailer;
use Onviser\Mailer\Socket\Socket;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SendToSocketTest extends TestCase
{
    protected Mail $mail;
    protected SocketMock $socket;
    protected MailerInterface $mailer;

    /**
     * @param bool $result
     * @param array<string> $dialog
     * @return bool
     */
    #[DataProvider('providerTestSend')]
    public function testSend(bool $result, array $dialog): bool
    {
        $this->socket->setDialog($dialog);
        $this->assertEquals($result, $this->mailer->send($this->mail));
        return true;
    }

    /**
     * @return array<int, array<int, array<string, string>|true>>
     */
    public static function providerTestSend(): array
    {
        $emailFrom = 'from@example.com';
        $emailTo = 'to@example.com';
        $login = 'login';
        $password = 'password';
        $helloName = Socket::HELLO_NAME;

        return [
            [
                true,
                [
                    ''                        => '220',
                    "EHLO $helloName"         => '250',
                    'AUTH LOGIN'              => '334',
                    base64_encode($login)     => '334',
                    base64_encode($password)  => '235',
                    "MAIL FROM: <$emailFrom>" => '250',
                    "RCPT TO: <$emailTo>"     => '250',
                    'DATA'                    => '354',
                    '.'                       => '250',
                    'QUIT'                    => '221'
                ]
            ]
        ];
    }

    public function setUp(): void
    {
        $emailFrom = 'from@example.com';
        $emailTo = 'to@example.com';
        $login = 'login';
        $password = 'password';
        $helloName = Socket::HELLO_NAME;

        $this->mail = (new Mail())
            ->setSubject('This is a subject')
            ->setBody('This is a body')
            ->setFrom($emailFrom)
            ->to($emailTo);

        $this->socket = (new SocketMock(login: $login, password: $password))
            ->setHelloName($helloName);

        $this->mailer = new SendToSocketMailer($this->socket);
    }

    public function tearDown(): void
    {
        unset($this->mail);
        unset($this->socket);
        unset($this->mailer);
    }
}