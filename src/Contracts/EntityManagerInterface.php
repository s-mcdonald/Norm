<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Contracts;

use SamMcDonald\Norm\Connection;
use SamMcDonald\Norm\Query;

interface EntityManagerInterface
{
    /**
     * Gets the database connection.
     */
    public function getConnection(): Connection;

    /**
     * Starts a transaction.
     */
    public function startTransaction(): void;

    /**
     * Rollback a transaction.
     */
    public function rollback(): void;

    /**
     * Commits a transaction.
     */
    public function commit(): void;

    /**
     * Find Entity by its identifier.
     */
    public function find(string $entityClass, int $id): object|null;

    /**
     * Find All Entities.
     */
    public function findAll(string $entityClass): array|null;

    /**
     * Creates a new Query object.
     */
    public function createQuery(string $ddl = ''): Query;

    /**
     * Lock the em.
     */
    public function lock(): void;
}
