<?php

namespace App\Repositories;

use App\Models\Commit;

interface CommitRepositoryInterface
{
    public function save(Commit $commit): void;

    public function saveBatch(array $commits): void;

    /**
     * @return Commit[]
     */
    public function findPaginated(int $offset, int $limit, ?string $platform = null, ?string $owner = null, ?string $repo = null): array;

    public function count(?string $platform, ?string $owner = null, ?string $repo = null): int;

    public function existsByHash(string $hash): bool;
}
