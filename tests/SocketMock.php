<?php declare(strict_types=1);

namespace Tests;

use Onviser\Mailer\Socket\Socket;
use Onviser\Mailer\Socket\SocketInterface;

class SocketMock implements SocketInterface
{
    protected string $command = '';

    /** @var array<string> */
    protected array $dialog = [];

    public function __construct(
        protected string $host = '127.0.0.1',
        protected int    $port = 25,
        protected string $login = 'guest',
        protected string $password = 'guest',
        protected string $helloName = Socket::HELLO_NAME,
        protected int    $timeout = 10
    )
    {
    }

    public function open(): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function put(string $command): bool
    {
        $this->command = $command;
        return true;
    }

    public function get(): string
    {
        if (array_key_exists($this->command, $this->dialog)) {
            return $this->dialog[$this->command];
        }
        return '';
    }

    /**
     * @param array<string> $dialog
     * @return $this
     */
    public function setDialog(array $dialog): self
    {
        $this->dialog = $dialog;
        return $this;
    }

    public function getError(): string
    {
        return '';
    }

    public function setHelloName(string $helloName): self
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
}