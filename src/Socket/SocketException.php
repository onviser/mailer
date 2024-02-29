<?php declare(strict_types=1);

namespace Onviser\Mailer\Socket;

use Exception;
use Throwable;

class SocketException extends Exception
{
    protected string $command = '';
    protected string $answer = '';

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 503, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function setCommand(string $command): self
    {
        $this->command = $command;
        return $this;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;
        return $this;
    }

    public function getMessageExtended(): string
    {
        return "command: $this->command, answer: $this->answer";
    }
}