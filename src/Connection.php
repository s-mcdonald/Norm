<?php

declare(strict_types=1);

namespace SamMcDonald\Norm;

use PDO;
use SamMcDonald\Norm\Drivers\MySql\MySql80PlatformDriver;
use SamMcDonald\Norm\Drivers\PlatformDriver;

class Connection
{
    private PlatformDriver|null $driver = null;

    private PDO|null $pdo = null;

    public function __construct(
        Configuration $configuration,
        PlatformDriver|null $driver = null
    ){

        $this->pdo = new PDO(
            $configuration->getDsn(),
            $configuration->getUsername(),
            $configuration->getPassword(),
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        /**
         * We currently bind a default MySQL driver and allow a null value
         * for the connection. Future versions will enforce the
         * requirement of specifying a driver.
         */
        if ($this->driver === null) {
            $this->driver = new MySql80PlatformDriver($this->pdo);
        }
    }

    public function close(): void
    {
        $this->pdo = null;
    }

    public function startTransaction(): void
    {
        $this->driver->startTransaction();
    }

    public function rollback(): void
    {
        $this->driver->rollBack();
    }

    public function commit(): void
    {
        $this->driver->commit();
    }

    public function getPlatform(): PlatformDriver
    {
        return $this->driver;
    }

    public function isConnected(): bool
    {
        return $this->pdo !== null;
    }
}
