<?php

declare(strict_types=1);

namespace SamMcDonald\Norm;

readonly class Query
{
    public function __construct(
        private string $ddl,
    ) {
    }
}
