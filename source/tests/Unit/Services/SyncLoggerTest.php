<?php

namespace Tests\Unit\Services;

use App\Services\SyncLogger;
use PHPUnit\Framework\TestCase;

class SyncLoggerTest extends TestCase
{
    private SyncLogger $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = new SyncLogger();
    }

    public function testInfoOutputsMessageWithNewline(): void
    {
        $message = "Test info message";

        $this->expectOutputString($message . "\n");

        $this->logger->info($message);
    }

    public function testErrorOutputsMessageWithErrorPrefixAndNewline(): void
    {
        $message = "Test error message";
        $expectedOutput = "ERROR: " . $message . "\n";

        $this->expectOutputString($expectedOutput);

        $this->logger->error($message);
    }

    public function testLogSyncResultsOutputsCompleteStatsReport(): void
    {
        $stats = [
            'fetched' => 100,
            'saved' => 75,
            'duplicates' => 25,
        ];

        $expectedOutput = "Sync completed:\n";
        $expectedOutput .= "-- Fetched: 100\n";
        $expectedOutput .= "-- Saved: 75\n";
        $expectedOutput .= "-- Duplicates skipped: 25\n";

        $this->expectOutputString($expectedOutput);

        $this->logger->logSyncResults($stats);
    }

    public function testLogSyncResultsWithZeroValues(): void
    {
        $stats = [
            'fetched' => 0,
            'saved' => 0,
            'duplicates' => 0,
        ];

        $expectedOutput = "Sync completed:\n";
        $expectedOutput .= "-- Fetched: 0\n";
        $expectedOutput .= "-- Saved: 0\n";
        $expectedOutput .= "-- Duplicates skipped: 0\n";

        $this->expectOutputString($expectedOutput);

        $this->logger->logSyncResults($stats);
    }

    public function testLogSyncResultsWithLargeNumbers(): void
    {
        $stats = [
            'fetched' => 10000,
            'saved' => 9500,
            'duplicates' => 500,
        ];

        $expectedOutput = "Sync completed:\n";
        $expectedOutput .= "-- Fetched: 10000\n";
        $expectedOutput .= "-- Saved: 9500\n";
        $expectedOutput .= "-- Duplicates skipped: 500\n";

        $this->expectOutputString($expectedOutput);

        $this->logger->logSyncResults($stats);
    }
}
