<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\NormPdo;

use PDO;
use PDOException;
use PDOStatement;

readonly class NormStatement
{
    public function __construct(
        private PDOStatement $statement
    ) {}

    public function fetchOne($params = null): array|null
    {
        if ($this->execute($params)) {
            return $this->statement->fetch(\PDO::FETCH_ASSOC);
        }
        return null;
    }

    public function fetchAllAssociated($params = null): array
    {
        if ($this->execute($params)) {
            return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function fetchColumn($columnNumber = 0, $params = null)
    {
        if ($this->execute($params)) {
            return $this->statement->fetchColumn($columnNumber);
        }
        return false;
    }

    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    public function bindValue($parameter, $value, $dataType = PDO::PARAM_STR): bool
    {
        return $this->statement->bindValue($parameter, $value, $dataType);
    }

    public function bindParam($parameter, &$variable, $dataType = PDO::PARAM_STR, $length = null): bool
    {
        return $this->statement->bindParam($parameter, $variable, $dataType, $length);
    }

    public function errorCode(): string|null
    {
        return $this->statement->errorCode();
    }

    public function errorInfo(): array
    {
        return $this->statement->errorInfo();
    }

    public function closeCursor(): bool
    {
        return $this->statement->closeCursor();
    }

    private function execute($params = null): bool
    {
        try {
            return $this->statement->execute($params);
        } catch (PDOException $e) {
            $this->handleException($e);
            return false;
        }
    }

    private function handleException(PDOException $e): void
    {
        error_log('PDOStatement error: ' . $e->getMessage());
    }
}
