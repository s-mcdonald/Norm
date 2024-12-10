<?php

declare(strict_types=1);

namespace SamMcDonald\Norm;

readonly class EntityRepository
{
    public function __construct(
        private EntityManager $entityManager,
        private string $entityClass,
    ){
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
}
