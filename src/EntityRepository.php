<?php

declare(strict_types=1);

namespace SamMcDonald\Norm;

/**
 * Class EntityRepository
 *
 * Provides a layer of abstraction for interacting with entities in the database.
 * Facilitates centralized access and manipulation of data through structured methods.
 */
readonly class EntityRepository
{
    public function __construct(
        private EntityManager $entityManager,
        private string $entityClass,
    ){
    }

    public function get($id): object|null
    {
        return $this->entityManager->get($this->entityClass, $id);
    }

    public function find($id): object|null
    {
        return $this->entityManager->find($this->entityClass, $id);
    }

    public function findAll(): array|null
    {
        return $this->entityManager->findAll($this->entityClass);
    }

    public function delete($id): bool
    {
        $model = $this->entityManager->find($this->entityClass, $id);
        if ($model === null) {
            return false;
        }

        return $this->entityManager->remove($model);
    }

    public function findOneBy(array $criteria): object|null
    {
        throw new \Exception('Not implemented');
    }
}
