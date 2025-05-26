<?php

namespace Tests\Unit\Services;

use App\Services\CommitSyncService;
use App\Api\ApiClientInterface;
use App\Repositories\CommitRepositoryInterface;
use App\Factories\CommitFactory;
use App\Services\BatchProcessor;
use App\Services\SyncLogger;
use PHPUnit\Framework\TestCase;

class CommitSyncServiceTest extends TestCase 
{
    public function testSyncCommitsReturnsSavedAndDuplicateCount()
    {
        // Mock all dependencies
        $mockApiClient = $this->createMock(ApiClientInterface::class);
        $mockRepo = $this->createMock(CommitRepositoryInterface::class);
        $mockFactory = $this->createMock(CommitFactory::class);
        $mockBatchProcessor = $this->createMock(BatchProcessor::class);
        $mockLogger = $this->createMock(SyncLogger::class);

        // Set up expectations
        $mockApiClient->expects($this->once())
                     ->method('getMostRecentCommits')
                     ->with('test', 'repo', 1)
                     ->willReturn([]);

        $mockLogger->expects($this->exactly(2))
                  ->method('info');

        $mockLogger->expects($this->once())
                  ->method('logSyncResults');

        $service = new CommitSyncService(
            $mockApiClient,
            $mockRepo,
            $mockFactory,
            $mockBatchProcessor,
            $mockLogger
        );

        $result = $service->syncCommits('test', 'repo', 1);
        
        $this->assertArrayHasKey('saved', $result);
        $this->assertArrayHasKey('duplicates', $result);
        $this->assertArrayHasKey('fetched', $result);
        $this->assertEquals(0, $result['fetched']);
        $this->assertEquals(0, $result['saved']);
        $this->assertEquals(0, $result['duplicates']);
    }
}
