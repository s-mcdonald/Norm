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
        $connection = new Connection($this->getValidConfig());

        static::assertTrue(
            $connection->isConnected()
        );
    }

    public function testConnectionCloseIsNotConnected(): void
    {
        $connection = new Connection($this->getValidConfig());

        static::assertTrue($connection->isConnected());

        $connection->close();

        static::assertFalse($connection->isConnected());
    }

    private function getValidConfig(): Configuration
    {
        $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=testdb;charset=utf8mb4';
        return new Configuration(
            $dsn,
            'root',
            'root'
        );
    }
}
