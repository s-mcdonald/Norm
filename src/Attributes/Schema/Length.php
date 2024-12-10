<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Attributes\Schema;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Length
{
    public function __construct(
        private int $length = 255,
    ) {
    }

    public function getLength(): int|null
    {
        return $this->length;
    }
}
