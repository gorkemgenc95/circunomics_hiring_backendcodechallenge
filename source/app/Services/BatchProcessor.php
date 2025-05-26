<?php

namespace App\Services;

use App\Repositories\CommitRepositoryInterface;

class BatchProcessor
{
    private CommitRepositoryInterface $repository;

    private int $batchSize;

    public function __construct(CommitRepositoryInterface $repository, int $batchSize = 100)
    {
        $this->repository = $repository;
        $this->batchSize = $batchSize;
    }

    public function saveCommits(array $commits): int
    {
        if (empty($commits)) {
            return 0;
        }

        $saved = 0;
        $batches = array_chunk($commits, $this->batchSize);

        foreach ($batches as $batch) {
            $this->repository->saveBatch($batch);
            $saved += count($batch);
        }

        return $saved;
    }

    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    public function setBatchSize(int $batchSize): void
    {
        $this->batchSize = $batchSize;
    }
}
