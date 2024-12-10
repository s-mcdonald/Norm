<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Drivers\Postgres;

use PDO;
use SamMcDonald\Norm\Drivers\PlatformDriver;

abstract class AbstractPostgresPlatformDriver extends PlatformDriver
{
    public function __construct(
        protected readonly PDO $pdo,
    ){
        //
    }

    public function startTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    protected function getObjectQuoteCharacter(): string
    {
        return '"';
    }
}
