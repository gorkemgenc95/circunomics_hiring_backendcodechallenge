<?php

namespace Tests\Unit\Repositories;

use App\Repositories\MySqlCommitRepository;
use App\Models\Commit;
use App\Config\Database;
use PHPUnit\Framework\TestCase;

class MySqlCommitRepositoryTest extends TestCase
{
    public function testConstructorAcceptsDependencies(): void
    {
        $mockDatabase = $this->createMock(Database::class);
        $mockCommit = $this->createMock(Commit::class);
        
        $mockDatabase->expects($this->once())
                    ->method('initialize');

        $repository = new MySqlCommitRepository($mockDatabase, $mockCommit);
        
        $this->assertInstanceOf(MySqlCommitRepository::class, $repository);
    }

    public function testConstructorUsesDefaultsWhenNullProvided(): void
    {
        $repository = new MySqlCommitRepository(null, null);
        
        $this->assertInstanceOf(MySqlCommitRepository::class, $repository);
    }
} 