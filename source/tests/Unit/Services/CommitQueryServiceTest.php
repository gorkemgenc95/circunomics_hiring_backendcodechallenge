<?php

namespace Tests\Unit\Services;

use App\Services\CommitQueryService;
use App\Repositories\CommitRepositoryInterface;
use App\Models\Commit;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommitQueryServiceTest extends TestCase
{
    private CommitQueryService $service;
    private CommitRepositoryInterface|MockObject $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepository = $this->createMock(CommitRepositoryInterface::class);
        $this->service = new CommitQueryService($this->mockRepository);
    }

    public function testGetPaginatedCommits(): void
    {
        $mockCommit = $this->createMock(Commit::class);

        $mockCommit->expects($this->any())
            ->method('getAttribute')
            ->willReturnMap([
                ['hash', 'abc123'],
                ['author', 'John Doe'],
                ['repository_owner', 'nodejs'],
                ['repository_name', 'node'],
                ['platform', 'github'],
                ['date', '2023-01-01 12:00:00']
            ]);

        $commits = [$mockCommit];
        
        $this->mockRepository
            ->expects($this->once())
            ->method('findPaginated')
            ->with(0, 20, 'github', 'nodejs', 'node')
            ->willReturn($commits);

        $result = $this->service->getPaginatedCommits(1, 20, 'github', 'nodejs', 'node');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('commits', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertCount(1, $result['commits']);
        $this->assertEquals('abc123', $result['commits'][0]['hash']);
    }

    public function testConstructorAcceptsRepository(): void
    {
        $mockRepository = $this->createMock(CommitRepositoryInterface::class);
        $service = new CommitQueryService($mockRepository);
        
        $this->assertInstanceOf(CommitQueryService::class, $service);
    }
} 