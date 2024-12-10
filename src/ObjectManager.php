<?php

declare(strict_types=1);

namespace SamMcDonald\Norm;

use SamMcDonald\Norm\Attributes\AttributeHelper;

/**
 * Manages objects, cache and all the other things the em doesn't want
 * to think about. You should not use the OM directly!
 */
class ObjectManager
{
    private array $entities = [];

    // not yet used. Will be used to store the state of the entity
    // for update/insert ect.
    // [ id1 => 'changed',... ]
    private array $entityState = [];

    public function flush(): void
    {
        foreach ($this->entities as $entity) {
            $this->persist($entity);
            $idValue = AttributeHelper::getPrimaryKey($entity);
            $this->removeFromCache($entity::class, $entity->{$idValue});
        }
    }

    protected function identify(object $object): object
    {
        return $this->entities[spl_object_id($object)] ?? $object;
    }

    protected function removeFromCache(string $entityClass, int $id): void
    {
        unset($this->entities[$this->getUniqueKey($entityClass, $id)]);
    }

    protected function getCachedEntity(string $entityClass, int $id): object|null
    {
        return $this->entities[$this->getUniqueKey($entityClass, $id)] ?? null;
    }

    protected function addEntity(object $object, string $entityClass, int $id): void
    {
        $this->entities[$this->getUniqueKey($entityClass, $id)] = $object;
    }

    protected static function mapToObject(array $data, string $entityClass): object
    {
        $object = new $entityClass();

        AttributeHelper::setInstanceVariables($object, $data);

        return $object;
    }

    private function getUniqueKey(string $entityClass, int $id): string
    {
        return $entityClass . '_' . $id;
    }
}
