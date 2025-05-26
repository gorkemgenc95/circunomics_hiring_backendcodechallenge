<?php

namespace App\Config;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    private DatabaseFactory $factory;

    private ?Capsule $connection = null;

    public function __construct(DatabaseFactory $factory = null)
    {
        $this->factory = $factory ?? new DatabaseFactory();
    }

    public function getConnection(): Capsule
    {
        if ($this->connection === null) {
            $this->connection = $this->factory->createConnection();
        }

        return $this->connection;
    }

    public function initialize(): void
    {
        $this->getConnection();
    }
}
