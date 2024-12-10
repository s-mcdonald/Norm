<?php

declare(strict_types=1);

namespace Tests\SamMcDonald\Norm\Unit;

use PHPUnit\Framework\TestCase;
use SamMcDonald\Norm\Configuration;
use SamMcDonald\Norm\Connection;

class ConnectionTest extends TestCase
{
    public function testNewConnectionSucceeds(): void
    {
        $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=testdb;charset=utf8mb4';
        $config = new Configuration(
            $dsn,
            'root',
            'root'
        );

        $connection = new Connection($config);

        static::assertTrue(
            $connection->isConnected()
        );
    }
}
