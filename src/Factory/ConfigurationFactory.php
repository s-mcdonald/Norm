<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Factory;

use SamMcDonald\Norm\Configuration;

/**
 * ConfigurationFactory class responsible for initializing configuration settings.
 *
 * This class provides methods to create configuration settings. It serves as a
 * centralized factory for configuration data, allowing the application to
 * obtain and manage its settings efficiently.
 */
class ConfigurationFactory
{
    public static function createConfiguration(): Configuration
    {
        return self::createConfigurationFromEnvironmentSettings();
    }

    public static function createConfigurationFromEnvironmentSettings(): Configuration
    {
        return new Configuration(
            $_ENV['NORM_DSN'],
            $_ENV['NORM_USERNAME'],
            $_ENV['NORM_PASSWORD']
        );
    }
}
