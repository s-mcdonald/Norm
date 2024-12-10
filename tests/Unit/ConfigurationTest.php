<?php

declare(strict_types=1);

namespace Tests\SamMcDonald\Norm\Unit;

use PHPUnit\Framework\TestCase;
use SamMcDonald\Norm\Configuration;

class ConfigurationTest extends TestCase
{
    public function testNewConfiguration(): void
    {
        $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=testdb;charset=utf8mb4';
        $config = new Configuration(
            $dsn,
            'root',
            'root'
        );

        static::assertEquals($dsn, $config->getDsn());
        static::assertEquals('root', $config->getUsername());
        static::assertEquals('root', $config->getPassword());
    }
}
