<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Checks;

use SamMcDonald\Norm\Attributes;
use SamMcDonald\Norm\Attributes\Orm\NormEntityTable;

class TableNameCheck
{
    public function __invoke(string|object $entityClass): string|null
    {
        $attributes = Reflector::show($entityClass)->getAttributes(NormEntityTable::class);

        if (false === empty($attributes)) {
            foreach ($attributes as $attribute) {
                $inst = $attribute->newInstance();
                if ($inst instanceof Attributes\Orm\NormEntityTable) {
                    return $inst->getTableName();
                }
            }
        }

        return null;
    }
}
