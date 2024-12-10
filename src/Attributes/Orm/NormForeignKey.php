<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Attributes\Orm;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class NormForeignKey
{
    public function __construct(
        public string $entity,
        public string $entityField,
    ) {}
}
