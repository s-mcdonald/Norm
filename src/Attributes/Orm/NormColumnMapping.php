<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Attributes\Orm;

use Attribute;
use SamMcDonald\Norm\Types\NormColType;

#[Attribute(Attribute::TARGET_PROPERTY)]
class NormColumnMapping
{
    public function __construct(
        public string $columnName,
        public NormColType $columnType = NormColType::String,
    ) {}

    public function getFieldName(): string
    {
        return $this->columnName;
    }

    public function getColumnType(): NormColType
    {
        return $this->columnType;
    }
}
