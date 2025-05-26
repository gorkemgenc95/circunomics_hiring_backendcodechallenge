<?php

namespace App\Services;

class SyncLogger
{
    public function info(string $message): void
    {
        echo $message . "\n";
    }

    public function error(string $message): void
    {
        echo "ERROR: " . $message . "\n";
    }

    public function logSyncResults(array $stats): void
    {
        $this->info("Sync completed:");
        $this->info("-- Fetched: {$stats['fetched']}");
        $this->info("-- Saved: {$stats['saved']}");
        $this->info("-- Duplicates skipped: {$stats['duplicates']}");
    }
}
