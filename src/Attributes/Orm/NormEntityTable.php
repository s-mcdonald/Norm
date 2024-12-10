<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Attributes\Orm;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class NormEntityTable
{
    public function __construct(
        private string $tableName
    ){
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }
}
