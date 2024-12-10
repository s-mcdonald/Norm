<?php

declare(strict_types=1);

namespace SamMcDonald\Norm;

readonly class Configuration
{
    public function __construct(
        private string $dsn,
        private string $username,
        private string $password = '',
    ) {
    }

    public static function create(string $dsn, string $username, string $password): self
    {
        return new self($dsn, $username, $password);
    }

    public function getDsn(): string
    {
        return $this->dsn;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
