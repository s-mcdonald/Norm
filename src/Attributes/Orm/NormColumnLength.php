<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Attributes\Orm;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class NormColumnLength
{
    public function __construct(
        private int $length = 255,
    ) {}

    public function getLength(): int
    {
        return $this->length;
    }
}
