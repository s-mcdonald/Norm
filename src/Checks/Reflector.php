<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Checks;

use ReflectionClass;

class Reflector
{
    public static function show(string|object $entityClass): ReflectionClass
    {
        if (is_string($entityClass)) {
            return new ReflectionClass($entityClass);
        }

        return new ReflectionClass($entityClass::class);
    }
}
