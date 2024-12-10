<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\NormPdo;

use PDO;

readonly class NormPdo
{
    public function __construct(
        private PDO $pdo,
    ) {}

    public function prepareNormStatement(string $sql): NormStatement
    {
        return new NormStatement(
            $this->pdo->prepare($sql)
        );
    }
}
