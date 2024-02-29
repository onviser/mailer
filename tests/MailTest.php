<?php declare(strict_types=1);

namespace Tests;

use Onviser\Mailer\Mail;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class MailTest extends TestCase
{
    /**
     * @param Mail $mail
     * @param array<string> $data
     * @return bool
     */
    #[DataProvider('providerTestConvertToString')]
    public function testConvertToString(Mail $mail, array $data): bool
    {
        $this->assertEquals($mail->__toString(), implode("\r\n", $data));
        return true;
    }

    /**
     * @return array<int, array<int, array<int, string>|Mail>>
     */
    public static function providerTestConvertToString(): array
    {
        $time = time();
        return [
            [
                (new Mail('This is a subject', 'This is a body', 'from@example.com', 'to@example.com'))
                    ->setTime($time),
                [
                    'MIME-Version: 1.0',
                    'Content-Type: text/plain; charset=utf-8',
                    'Content-Transfer-Encoding: 8bit',
                    'From: from@example.com',
                    'Subject: =?utf-8?B?VGhpcyBpcyBhIHN1YmplY3Q=?=',
                    'To: to@example.com',
                    'Date: ' . date('D, d M Y H:i:s', $time),
                    '',
                    'This is a body',
                    '',
                    ''
                ]
            ],
            [
                (new Mail('This is a subject', 'This is a body', 'from@example.com', 'to@example.com'))
                    ->setReplayTo('replay@example.com')
                    ->setTime($time),
                [
                    'MIME-Version: 1.0',
                    'Content-Type: text/plain; charset=utf-8',
                    'Content-Transfer-Encoding: 8bit',
                    'From: from@example.com',
                    'Reply-To: replay@example.com',
                    'Subject: =?utf-8?B?VGhpcyBpcyBhIHN1YmplY3Q=?=',
                    'To: to@example.com',
                    'Date: ' . date('D, d M Y H:i:s', $time),
                    '',
                    'This is a body',
                    '',
                    ''
                ]
            ]
        ];
    }
}
