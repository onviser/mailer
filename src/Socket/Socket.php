<?php declare(strict_types=1);

namespace Onviser\Mailer\Socket;

class Socket implements SocketInterface
{
    protected const string EOL = "\r\n";

    protected const string ERROR_CONNECTION = "socket connection error, code: %s, message: %s";
    protected const string ERROR_REQUEST = "socket request error";
    protected const string ERROR_RESPONSE = "socket response error";

    public const string HELLO_NAME = 'Onviser SMTP Client';

    /**
     * @var resource
     */
    protected $socket;

    protected string $error = '';

    public function __construct(
        protected string $host = '127.0.0.1',
        protected int    $port = 25,
        protected string $login = 'guest',
        protected string $password = 'guest',
        protected string $helloName = self::HELLO_NAME,
        protected int    $timeout = 10
    )
    {
    }

    public function setHelloName(string $helloName): static
    {
        $this->helloName = $helloName;
        return $this;
    }

    public function getHelloName(): string
    {
        return $this->helloName;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function open(): bool
    {
        $resource = fsockopen($this->host, $this->port, $errorCode, $errorMessage, $this->timeout);
        if ($resource === false) {
            $this->error = sprintf(self::ERROR_CONNECTION, $errorCode, $errorMessage);
            return false;
        }
        $this->socket = $resource;
        return true;
    }

    public function close(): bool
    {
        return is_resource($this->socket) && fclose($this->socket);
    }

    public function put(string $command): bool
    {
        if (fputs($this->socket, $command . self::EOL) === false) {
            $this->error = self::ERROR_REQUEST;
            return false;
        }
        return true;
    }

    public function get(): string
    {
        $answer = '';
        while ($line = fgets($this->socket)) {
            $answer .= $line;
            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }
        return $answer;
    }

    public function getError(): string
    {
        return $this->error;
    }
}