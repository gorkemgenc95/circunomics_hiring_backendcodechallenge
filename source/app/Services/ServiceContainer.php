<?php

namespace App\Services;

use App\Api\GitHubApiClient;
use App\Factories\CommitFactory;
use App\Models\Commit;
use App\Repositories\MySqlCommitRepository;
use App\Config\Database;

class ServiceContainer
{
    private static ?self $instance = null;
    private array $services = [];

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getGitHubCommitSyncService(): CommitSyncService
    {
        if (!isset($this->services['ghCommitSyncService'])) {
            $this->services['ghCommitSyncService'] = new CommitSyncService(
                new GitHubApiClient(),
                $this->getCommitRepository(),
                $this->getCommitFactory(),
                $this->getBatchProcessor(),
                $this->getSyncLogger()
            );
        }
        return $this->services['ghCommitSyncService'];
    }

    public function getCommitQueryService(): CommitQueryService
    {
        if (!isset($this->services['commitQueryService'])) {
            $this->services['commitQueryService'] = new CommitQueryService(
                $this->getCommitRepository()
            );
        }
        return $this->services['commitQueryService'];
    }

    private function getCommitRepository()
    {
        if (!isset($this->services['commitRepository'])) {
            $this->services['commitRepository'] = new MySqlCommitRepository(
                $this->getDatabase(),
                $this->getCommitModel()
            );
        }
        return $this->services['commitRepository'];
    }

    private function getDatabase()
    {
        if (!isset($this->services['database'])) {
            $this->services['database'] = new Database();
        }
        return $this->services['database'];
    }

    private function getCommitFactory()
    {
        if (!isset($this->services['commitFactory'])) {
            $this->services['commitFactory'] = new CommitFactory();
        }
        return $this->services['commitFactory'];
    }

    private function getBatchProcessor()
    {
        if (!isset($this->services['batchProcessor'])) {
            $this->services['batchProcessor'] = new BatchProcessor(
                $this->getCommitRepository(),
                100
            );
        }
        return $this->services['batchProcessor'];
    }

    private function getSyncLogger()
    {
        if (!isset($this->services['syncLogger'])) {
            $this->services['syncLogger'] = new SyncLogger();
        }
        return $this->services['syncLogger'];
    }

    private function getCommitModel()
    {
        if (!isset($this->services['commitModel'])) {
            $this->services['commitModel'] = new Commit();
        }
        return $this->services['commitModel'];
    }
} 