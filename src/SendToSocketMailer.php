<?php declare(strict_types=1);

namespace Onviser\Mailer;

use Onviser\Mailer\Socket\SocketException;
use Onviser\Mailer\Socket\SocketInterface;

class SendToSocketMailer implements MailerInterface
{
    /**
     * @var string[]
     */
    protected array $log = [];

    protected string $error = '';

    public function __construct(
        protected SocketInterface $socket
    )
    {
    }

    public function send(Mail $mail): bool
    {
        if ($this->socket->open() === false) {
            $this->error = $this->socket->getError();
            return false;
        }
        $result = false;
        try {
            $this->command(220);
            $this->command(250, 'EHLO ' . $this->socket->getHelloName());
            $this->command(334, 'AUTH LOGIN');
            $this->command(334, base64_encode($this->socket->getLogin()));
            $this->command(235, base64_encode($this->socket->getPassword()));
            $this->command(250, 'MAIL FROM: <' . $mail->getFrom() . '>');
            $to = array_merge(
                $mail->getTo(),
                $mail->getCopy(),
                $mail->getBlindCopy()
            );
            foreach ($to as $email) {
                $this->command(250, 'RCPT TO: <' . $email . '>');
            }
            $this->command(354, 'DATA');
            $this->socket->put($mail->__toString());
            $this->command(250, '.');
            $this->command(221, 'QUIT');
            $result = true;
        } catch (SocketException $e) {
            $this->log('error', $e->getMessageExtended());
        } finally {
            $this->socket->close();
        }
        return $result;
    }

    /**
     * @param int $expectedCode
     * @param string $command
     * @return bool
     * @throws SocketException
     */
    protected function command(int $expectedCode, string $command = ''): bool
    {
        if ($command !== '') {
            $this->log('client', $command);
            $this->socket->put($command);
        }
        $answer = $this->socket->get();
        $this->log('server', $answer);
        $code = intval(substr($answer, 0, 3));
        if ($code !== $expectedCode) {
            $this->socket->put('RSET');
            $this->log('client', 'RSET');
            $this->log('server', $this->socket->get());
            $this->socket->put('QUIT');
            $this->log('client', 'QUIT');
            $this->log('server', $this->socket->get());
            $this->socket->close();
            $this->error = "command: $command, expected code: $expectedCode, answer: $answer";
            throw (new SocketException())
                ->setCommand($command)
                ->setAnswer($answer);
        }

        return true;
    }

    /**
     * @param string $prefix
     * @param string|null $message
     * @return $this
     */
    protected function log(string $prefix, ?string $message = ''): self
    {
        $this->log[] = $prefix . (isset($message) ? ': ' . $message : '');
        return $this;
    }

    /**
     * @return string[]
     */
    public function getLog(): array
    {
        return $this->log;
    }

    public function getError(): string
    {
        return $this->error;
    }
}