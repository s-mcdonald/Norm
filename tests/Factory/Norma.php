<?php

declare(strict_types=1);

namespace Tests\SamMcDonald\Norm\Factory;

use SamMcDonald\Norm\Configuration;
use SamMcDonald\Norm\Connection;

class Norma
{
    public static function createValidConnection(): Connection
    {
        return new Connection(self::createValidConfiguration());
    }

    private static function createValidConfiguration(): Configuration
    {
        $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=testdb;charset=utf8mb4';
        return new Configuration(
            $dsn,
            'root',
            'root'
        );
    }
}
