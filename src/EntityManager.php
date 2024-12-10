<?php

declare(strict_types=1);

namespace SamMcDonald\Norm;

use Exception;
use InvalidArgumentException;
use SamMcDonald\Norm\Attributes\AttributeHelper;
use SamMcDonald\Norm\Checks\PrimaryKeyCheck;
use SamMcDonald\Norm\Contracts\EntityManagerInterface;
use SamMcDonald\Norm\Exceptions\NormException;

/**
 * Class EntityManager
 *
 * Manages the lifecycle of entities and serves as the primary interface
 * for accessing and interacting with the database layer in an application.
 *
 * The EntityManager acts as a bridge between the application objects and the database,
 * fostering abstraction and robustness in data management.
 */
class EntityManager extends ObjectManager implements EntityManagerInterface
{
    private bool $lockedDueToError = false;

    public function __construct(
        private readonly Connection $connection,
    ){
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function startTransaction(): void
    {
        $this->getConnection()->startTransaction();
    }

    public function rollback(): void
    {
        $this->getConnection()->rollback();
    }

    public function commit(): void
    {
        $this->getConnection()->commit();
    }

    /**
     * @throws \Exception
     */
    public function persist(object $model): bool
    {
        $tableName = AttributeHelper::getTableName($model);
        $fields = AttributeHelper::getMappedFieldValues($model);
        $idValue = AttributeHelper::getPrimaryKey($model);
        $columns = array_keys($fields);

        return $this->doPersist(
            $idValue,
            $tableName,
            $columns,
            $fields,
        );
    }

    private function doPersist($id, $table, $columns, $fields): bool
    {
        $platform = $this->getConnection()->getPlatform();
        if (isset($id) && $platform->queryCheckEntityExist($id, $table)) {
            throw NormException::createEntityOutOfBounds();
        }

        if (isset($id)) {
            $platform->queryUpdateEntity($id, $table, $columns, $fields);
        }

        return $platform->queryInsertEntity($table, $columns, $fields);
    }

    /**
     * @throws Exception
     */
    public function getRepository(string $entityClass): EntityRepository
    {
        if (false === AttributeHelper::isEntityClass($entityClass)) {
            throw new InvalidArgumentException("Class is not an entity class.");
        }

        return new EntityRepository($this, $entityClass);
    }

    public function get(string $entityClass, int $id): object|null
    {
        $found = $this->find($entityClass, $id);

        if ($found === null) {
            throw new Exception('Entity not found.');
        }

        return $found;
    }

    /**
     * @throws Exception
     */
    public function find(string $entityClass, int $id): object|null
    {
        if (false === AttributeHelper::isEntityClass($entityClass)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Class: `%s` is not an entity.",
                    $entityClass
                ),
            );
        }

        return $this->doFind($entityClass, $id);
    }

    public function findAll(string $entityClass): array|null
    {
        if (false === AttributeHelper::isEntityClass($entityClass)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Class: `%s` is not an entity.",
                    $entityClass,
                ),
            );
        }

        return $this->doFindAll($entityClass);
    }

    public function remove(object $model): bool
    {
        if (false === AttributeHelper::isEntityClass($model)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Class: `%s` is not an entity.",
                    $model::class,
                ),
            );
        }

        // check to ensure prim key
        if (false === (new PrimaryKeyCheck())($model)) {
            throw new InvalidArgumentException("Entity does not have a unique identifier.");
        }

        $tableName = AttributeHelper::getTableName($model);

        $idField = AttributeHelper::getPrimaryKeyFieldNames($model);

        // we need to remove from underlying cache
        $this->removeFromCache($model::class, $model->{$idField});

        return $this->connection->getPlatform()->queryDeleteEntity($model->{$idField}, $tableName);
    }

    public function createQuery(string $ddl = ''): Query
    {
        return new Query($dql);
    }

    public function lock(): void
    {
        $this->lockedDueToError = true;
    }

    public function isLocked(): bool
    {
        return $this->lockedDueToError;
    }

    /**
     * Get all in cache and ensure is persisted.
     * Clear the cache once done.
     */
    public function flush(): void
    {
        parent::flush();
    }


    public function clear(): void
    {
        throw new \RuntimeException('Not yet implemented');
    }

    public function doFind(string $entityClass, int $id): object|null
    {
        if (false === AttributeHelper::isEntityClass($entityClass)) {
            throw new InvalidArgumentException("Class is not an entity class.");
        }

        $tableName = AttributeHelper::getTableName($entityClass);

        $cachedObject = $this->getCachedEntity($entityClass, $id);
        if ($cachedObject !== null) {
            return $cachedObject;
        }

        $data = $this->connection->getPlatform()->queryGetEntity($id, $tableName);

        if ($data === null) {
            return null;
        }

        $object = self::mapToObject($data, $entityClass);

        return $this->identify($object);
    }

    public function doFindAll(string $entityClass): array|null
    {
        if (false === AttributeHelper::isEntityClass($entityClass)) {
            throw new InvalidArgumentException("Class is not an entity class.");
        }

        $tableName = AttributeHelper::getTableName($entityClass);

        $data = $this->connection->getPlatform()->queryGetAllEntities($tableName);

        if ($data === null) {
            return null;
        }

        $results = [];

        $idField = AttributeHelper::getPrimaryKeyFieldNames($entityClass);

        foreach ($data as $entityData) {
            $objectId = $entityData[$idField];
            $cachedObject = $this->getCachedEntity($entityClass, $objectId);

            if ($cachedObject !== null) {
                $results[] = $cachedObject;
                continue;
            }

            $object = self::mapToObject($entityData, $entityClass);

            $results[] = $object;
            $this->addEntity($object, $entityClass, $objectId);
        }

        return $results;
    }
}
