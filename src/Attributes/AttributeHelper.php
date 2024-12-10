<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Attributes;

use SamMcDonald\Norm\Attributes\Orm\NormColumnMapping;
use SamMcDonald\Norm\Attributes\Orm\NormEntity;
use SamMcDonald\Norm\Attributes\Orm\NormEntityTable;
use SamMcDonald\Norm\Attributes\Orm\NormPrimaryKey;
use SamMcDonald\Norm\Checks\Reflector;

/**
 * Need to refactor this code out of this helper file.
 */
class AttributeHelper
{
    public static function getTableName(string|object $entityClass): string|null
    {
        $reflection = Reflector::show($entityClass);

        if (false === self::isEntityClass($entityClass)) {
            // We should probably throw an exception here since we do not expect
            // to get a object that is not an entity class.
            return null;
        }

        $attributes = $reflection->getAttributes(NormEntityTable::class);

        if (false === empty($attributes)) {
            foreach ($attributes as $attribute) {
                $inst = $attribute->newInstance();
                if ($inst instanceof NormEntityTable) {
                    return $inst->getTableName();
                }
            }
        }

        return null;
    }

    public static function isEntityClass(string|object $entityClass): bool
    {
        $reflection = Reflector::show($entityClass);

        $attributes = $reflection->getAttributes(NormEntity::class);

        return count($attributes) > 0;
    }

    public static function getMappedFields(string|object $entityClass): array
    {
        $columns = [];
        $reflection = Reflector::show($entityClass);

        $attributes = $reflection->getAttributes(NormColumnMapping::class);

        if (false === empty($attributes)) {
            return $columns;
        }

        foreach ($attributes as $attribute) {
            $inst = $attribute->newInstance();
            if ($inst instanceof NormColumnMapping) {
                $columns[] = $inst->columnName;
            }
        }

        return $columns;
    }

    public static function setInstanceVariables(object $entityObject, array $data): void
    {
        $reflectionClass = Reflector::show($entityObject);

        foreach ($data as $key => $value) {
            if ($reflectionClass->hasProperty($key)) {
                $property = $reflectionClass->getProperty($key);

                if ($property->isPublic()) {
                    $entityObject->$key = $value;
                    continue;
                }

                $property->setAccessible(true);
                $property->setValue($entityObject, $value);
                $property->setAccessible(false);
            }
        }
    }

    public static function getMappedFieldValues(object $entityObject): array
    {
        $columns = [];
        $reflectionClass = Reflector::show($entityObject);

        $attributes = $reflectionClass->getAttributes(NormColumnMapping::class);

        if (false === empty($attributes)) {
            return $columns;
        }

        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes(NormColumnMapping::class);

            foreach ($attributes as $attribute) {
                $inst = $attribute->newInstance();
                if ($inst instanceof NormColumnMapping) {
                    $property->setAccessible(true);
                    $columns[$inst->getFieldName()] = null;
                    if ($property->isInitialized($entityObject)) {
                        $columns[$inst->getFieldName()] = $property->getValue($entityObject);
                    }

                    $property->setAccessible(false);
                }
            }
        }

        return $columns;
    }

    public static function getPrimaryKeyFieldNames(string|object $entityObject): string|null
    {
        $reflectionClass = Reflector::show($entityObject);

        foreach ($reflectionClass->getProperties() as $property) {
            if ($property->getAttributes(NormPrimaryKey::class)) {
                $columnMappingAttributes = $property->getAttributes(NormColumnMapping::class);
                if ($columnMappingAttributes) {
                    return $columnMappingAttributes[0]->newInstance()->getFieldName();
                }
            }
        }

        return null;
    }

    public static function getPrimaryKey(object $entityObject)
    {
        $reflectionClass = Reflector::show($entityObject);

        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes(NormPrimaryKey::class);
            if(count($attributes) && $property->isInitialized($entityObject)) {
                return $property->getValue($entityObject);
            }
        }

        return null;
    }
}
