<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Drivers\Postgres;

use PDO;

class PostgresPlatformDriver extends AbstractPostgresPlatformDriver
{
    public function queryGetAllEntities(string $tableName): ?array
    {
        $stmt = $this->pdo->prepare(
            sprintf(
                'SELECT * FROM %s',
                $this->quoteIdentifier($tableName),
            )
        );

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data ?: null;
    }

    public function queryGetEntity(mixed $id, string $tableName): ?array
    {
        $stmt = $this->pdo->prepare(
            sprintf(
                'SELECT * FROM %s WHERE id = :id',
                $this->quoteIdentifier($tableName)
            )
        );

        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ?: null;
    }

    public function queryCheckEntityExist(mixed $id, string $tableName): bool
    {
        $stmt = $this->pdo->prepare(
            sprintf(
                'SELECT id FROM %s WHERE id = :id',
                $this->quoteIdentifier($tableName)
            )
        );

        $stmt->execute(['id' => $id]);
        return (bool) $stmt->fetchColumn();
    }

    public function queryInsertEntity(string $tableName, array $columns, array $fields): bool
    {
        $colMeta = implode(', ', array_map(fn($col) => $this->quoteIdentifier($col), $columns));
        $valMeta = implode(', ', array_map(static fn($col) => ":$col", $columns));

        $stmt = $this->pdo->prepare(
            sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                $this->quoteIdentifier($tableName),
                $colMeta,
                $valMeta
            )
        );

        return $stmt->execute($fields);
    }

    public function queryUpdateEntity(mixed $id, string $tableName, array $columns, array $fields): bool
    {
        $valMeta = implode(', ', array_map(fn($col) => $this->quoteIdentifier($col) . " = :$col", $columns));
        $fields['id'] = $id;

        $stmt = $this->pdo->prepare(
            sprintf(
                'UPDATE %s SET %s WHERE id = :id',
                $this->quoteIdentifier($tableName),
                $valMeta
            )
        );

        return $stmt->execute($fields);
    }

    public function queryDeleteEntity(mixed $id, string $tableName): bool
    {
        $stmt = $this->pdo->prepare(
            sprintf(
                'DELETE FROM %s WHERE id = :id',
                $this->quoteIdentifier($tableName)
            )
        );

        return $stmt->execute(['id' => $id]);
    }

    public function truncate(string $tableName, bool $disableFkCheck = false): void
    {
        $stmt = $this->pdo->prepare(
            sprintf(
                'TRUNCATE %s RESTART IDENTITY CASCADE',
                $this->quoteIdentifier($tableName)
            )
        );

        $stmt->execute();
    }

    private function quoteIdentifier(string $objectName): string
    {
        if (
            str_starts_with($objectName, '"') &&
            str_ends_with($objectName, '"')
        ) {
            return $objectName;
        }

        return $this->getObjectQuoteCharacter(). $objectName . $this->getObjectQuoteCharacter();
    }

    public function queryListOfTableObjects(): array
    {
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this
            ->pdo
            ->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
        $statement->execute();
        return $statement
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function queryDefaultDatabase(): string
    {
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this
            ->pdo
            ->query("SELECT current_database()");
        $statement->execute();
        return $statement
            ->fetchColumn();
    }
}
