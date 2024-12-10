<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Attributes\Schema;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class NotNull
{
    public function __construct(
    ) {
    }
}
