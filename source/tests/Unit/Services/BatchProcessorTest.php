<?php

namespace Tests\Unit\Services;

use App\Models\Commit;
use App\Repositories\CommitRepositoryInterface;
use App\Services\BatchProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BatchProcessorTest extends TestCase
{
    private CommitRepositoryInterface|MockObject $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepository = $this->createMock(CommitRepositoryInterface::class);
    }

    public function testSaveCommitsWithEmptyArray(): void
    {
        $processor = new BatchProcessor($this->mockRepository, 2);

        $result = $processor->saveCommits([]);

        $this->assertEquals(0, $result);
    }

    public function testSaveCommitsWithSingleBatch(): void
    {
        $processor = new BatchProcessor($this->mockRepository, 5);

        $commits = [
            $this->createMock(Commit::class),
            $this->createMock(Commit::class),
        ];

        $this->mockRepository
            ->expects($this->once())
            ->method('saveBatch')
            ->with($commits);

        $result = $processor->saveCommits($commits);

        $this->assertEquals(2, $result);
    }

    public function testSaveCommitsWithMultipleBatches(): void
    {
        $processor = new BatchProcessor($this->mockRepository, 2);

        $commits = [
            $this->createMock(Commit::class),
            $this->createMock(Commit::class),
            $this->createMock(Commit::class),
            $this->createMock(Commit::class),
            $this->createMock(Commit::class),
        ];

        // Expect 3 batches: [2, 2, 1]
        $this->mockRepository
            ->expects($this->exactly(3))
            ->method('saveBatch');

        $result = $processor->saveCommits($commits);

        $this->assertEquals(5, $result);
    }

    public function testGetAndSetBatchSize(): void
    {
        $processor = new BatchProcessor($this->mockRepository, 100);

        $this->assertEquals(100, $processor->getBatchSize());

        $processor->setBatchSize(50);
        $this->assertEquals(50, $processor->getBatchSize());
    }

    public function testConstructorAcceptsRepository(): void
    {
        $mockRepository = $this->createMock(CommitRepositoryInterface::class);
        $processor = new BatchProcessor($mockRepository, 100);

        $this->assertInstanceOf(BatchProcessor::class, $processor);
    }
}
