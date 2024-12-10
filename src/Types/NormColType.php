<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Types;

enum NormColType: string
{
    case String = 'VARCHAR';
    case Integer = 'INTEGER';
}
