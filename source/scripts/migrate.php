<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;

$database = new Database();
$database->initialize();

echo "Running database migrations...\n";

try {
    $migrationFiles = glob(__DIR__ . '/../database/migrations/*.php');
    
    foreach ($migrationFiles as $file) {
        echo "Running migration: " . basename($file) . "\n";
        require_once $file;
        
        $className = pathinfo($file, PATHINFO_FILENAME);
        // Extract class name from filename
        $className = preg_replace('/^\d+_/', '', $className);
        $className = str_replace('_', '', ucwords($className, '_'));
        
        if (class_exists($className)) {
            $migration = new $className();
            $migration->up();
            echo "Migration completed: $className\n";
        }
    }
    echo "\nAll migrations completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
} 