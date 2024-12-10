<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Drivers;

abstract class PlatformDriver
{
    protected bool $supportsTransactions = true;

    // if words here
    public array $reservedKeywords = [];

    abstract public function startTransaction(): void;

    abstract public function rollback(): void;

    abstract public function commit(): void;

    abstract public function queryGetEntity(mixed $id, string $tableName): array|null;

    abstract public function queryGetAllEntities(string $tableName): null|array;

    abstract public function queryCheckEntityExist(mixed $id, string $tableName): bool;

    abstract public function queryInsertEntity(string $tableName, array $columns, array $fields): bool;

    abstract public function queryUpdateEntity(mixed $id, string $tableName, array $columns, array $fields): bool;

    abstract public function queryDeleteEntity(mixed $id, string $tableName): bool;

    abstract public function truncate(string $tableName, bool $disableFkCheck = false): void;

    abstract public function queryListOfTableObjects(): array;

    abstract public function queryDefaultDatabase(): string;
}
