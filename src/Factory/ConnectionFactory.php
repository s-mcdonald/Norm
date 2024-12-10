<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Factory;

use SamMcDonald\Norm\Connection;

class ConnectionFactory
{
    public static function createConnection(): Connection
    {
        return new Connection(
            ConfigurationFactory::createConfiguration()
        );
    }
}
