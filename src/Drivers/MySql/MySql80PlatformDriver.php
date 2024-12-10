<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Drivers\MySql;

use PDO;

class MySql80PlatformDriver extends AbstractMySqlPlatformDriver
{
    public function queryGetAllEntities(string $tableName): array|null
    {
        $stmt = $this->pdo->prepare(
            $this->getAllRowsFromSql($tableName)
        );

        $stmt->execute([]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function queryGetEntity(mixed $id, string $tableName): array|null
    {
        $sql = $this->getSqlGetRowByIdSql($tableName, 'id');
        $stmt = $this->pdo->prepare($sql . ' :id');

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (is_array($data)) {
            return $data;
        }

        return null;
    }

    public function queryCheckEntityExist(mixed $id, string $tableName): bool
    {
        // we need to remove the fixed id field for entities
        $sql = $this->getSqlCheckRowExistSql($tableName, 'id');
        $stmt = $this->pdo->prepare($sql . " :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return !($stmt->execute() &&
            $stmt->fetch(PDO::FETCH_COLUMN) !== $id);
    }

    public function queryInsertEntity(string $tableName, array $columns, array $fields): bool
    {
        $colMeta = implode(", ", $columns);
        $valMeta = implode(", ", array_map(static fn($col) => ":$col", $columns));

        return $this->pdo->prepare(
            sprintf(
                "INSERT INTO " . $tableName . " (" . $colMeta . ") VALUES (" . $valMeta . ")",
                $this->quoteIdentifier($tableName),
            )
        )->execute($fields);
    }

    public function queryUpdateEntity(mixed $id, string $tableName, array $columns, array $fields): bool
    {
        $valMeta = implode(", ", array_map(static fn($col) => "$col = :$col", $columns));

        return $this->pdo->prepare(
            sprintf(
                "UPDATE %s SET " . $valMeta . " WHERE id = :id",
                $this->quoteIdentifier($tableName),
            )
        )->execute($fields);
    }

    public function queryDeleteEntity(mixed $id, string $tableName): bool
    {
        return $this->pdo->prepare(
                $this->getDeleteByIdSql($tableName, ['id' => $id])
        )->execute(['id' => $id]);
    }

    public function truncate(string $tableName, bool $disableFkCheck = false): void
    {
        $sql = $this->getTruncateTableSql($tableName);

        if (false === $disableFkCheck) {
            $this->pdo->exec($sql);
            return;
        }

        $this->execWhileDisableForeignKeyChecks($this->pdo->prepare($sql));
    }

    public function queryListOfTableObjects(): array
    {
        try {
            $statement = $this->pdo->prepare(self::SQL_GET_ALL_BASE_TABLES);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error fetching list of table objects: ' . $e->getMessage());
            return [];
        }
    }

    public function queryDefaultDatabase(): string
    {
        return $this->pdo->query(
            $this->getDefaultDatabase()
        )->fetchColumn();
    }
}
