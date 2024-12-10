<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Attributes\Assert;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Length
{
    public function __construct(
        private int|null $minLength = null,
        private int|null $maxLength = null,
    ) {
    }

    public function getMinLength(): int|null
    {
        return $this->minLength;
    }

    public function getMaxLength(): int|null
    {
        return $this->maxLength;
    }
}
