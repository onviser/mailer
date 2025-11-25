<?php declare(strict_types=1);

namespace Onviser\Mailer\Socket;

interface SocketInterface
{
    public function open(): bool;

    public function close(): bool;

    public function put(string $command): bool;

    public function get(): string;

    public function getError(): string;

    public function setHelloName(string $helloName): static;

    public function getHelloName(): string;

    public function getLogin(): string;

    public function getPassword(): string;
}