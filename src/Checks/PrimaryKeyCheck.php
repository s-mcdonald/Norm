<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Checks;

use SamMcDonald\Norm\Attributes\Orm\NormPrimaryKey;

class PrimaryKeyCheck
{
    public function __invoke(string|object $entityClass): bool
    {
        return count(Reflector::show($entityClass)->getAttributes(NormPrimaryKey::class)) > 0;
    }
}
