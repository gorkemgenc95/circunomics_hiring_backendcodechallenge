<?php

namespace App\Config;

use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;
use InvalidArgumentException;

class DatabaseFactory
{
    private ?Capsule $capsule = null;
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->loadEnvironmentVariables();
    }

    public function createConnection(): Capsule
    {
        if ($this->capsule !== null) {
            return $this->capsule;
        }

        $config = [
            'driver' => $this->getConfig('DB_CONNECTION', 'mysql'),
            'host' => $this->getConfig('DB_HOST', 'localhost'),
            'port' => $this->getConfig('DB_PORT', '3306'),
            'database' => $this->getConfig('DB_DATABASE', 'git_api_service_db'),
            'username' => $this->getConfig('DB_USERNAME', 'user'),
            'password' => $this->getConfig('DB_PASSWORD', 'password123'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ];

        $this->validateConfig($config);

        $this->capsule = new Capsule();
        $this->capsule->addConnection($config);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();

        return $this->capsule;
    }

    private function loadEnvironmentVariables(): void
    {
        if (file_exists(__DIR__ . '/../../.env')) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
            $dotenv->load();
        }
    }

    private function getConfig(string $key, string $default = ''): string
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return $_ENV[$key] ?? getenv($key) ?: $default;
    }

    private function validateConfig(array $config): void
    {
        $required = ['driver', 'host', 'database', 'username'];
        
        foreach ($required as $field) {
            if (empty($config[$field])) {
                throw new InvalidArgumentException("Database config missing required field: {$field}");
            }
        }
    }
} 