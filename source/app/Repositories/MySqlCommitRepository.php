<?php

namespace App\Repositories;

use App\Models\Commit;
use App\Config\Database;
use DateTime;

class MySqlCommitRepository implements CommitRepositoryInterface
{
    private Database $database;
    private Commit $commitModel;

    public function __construct(?Database $database = null, ?Commit $commitModel = null)
    {
        $this->database = $database ?? new Database();
        $this->commitModel = $commitModel ?? new Commit();
        $this->database->initialize();
    }

    public function save(Commit $commit): void
    {
        $data = $this->adjustCommitDateFormat($commit);
        $this->commitModel->newQuery()->insert($data);
    }

    public function saveBatch(array $commits): void
    {
        if (empty($commits)) {
            return;
        }

        $chunks = array_chunk($commits, 100);
        
        foreach ($chunks as $chunk) {
            $data = [];
            foreach ($chunk as $commit) {
                if ($commit instanceof Commit) {
                    $data[] = $this->adjustCommitDateFormat($commit);
                }
            }
            
            if (!empty($data)) {
                $this->commitModel->newQuery()->insert($data);
            }
        }
    }

    public function find(string $platform, string $owner, string $repo, int $limit = 1000): array
    {
        return $this->commitModel->newQuery()
                    ->forRepository($platform, $owner, $repo)
                    ->mostRecent($limit)
                    ->get()
                    ->all();
    }

    public function existsByHash(string $hash): bool
    {
        return $this->commitModel->newQuery()
                    ->where('hash', $hash)
                    ->exists();
    }

    public function getMostRecent(int $limit = 1000): array
    {
        return $this->commitModel->newQuery()
                    ->mostRecent($limit)
                    ->get()
                    ->all();
    }

    public function count(?string $platform = 'github', ?string $owner = null, ?string $repo = null): int
    {
        $query = $this->commitModel->newQuery();
        
        if ($owner && $repo) {
            $query->forRepository($platform, $owner, $repo);
        }
        
        return $query->count();
    }

    public function findPaginated(int $offset, int $limit, ?string $platform = 'github', ?string $owner = null, ?string $repo = null): array
    {
        $query = $this->commitModel->newQuery();
        
        if ($platform && $owner && $repo) {
            $query->forRepository($platform, $owner, $repo);
        }
        
        return $query->orderBy('date', 'desc')
                    ->offset($offset)
                    ->limit($limit)
                    ->get()
                    ->all();
    }

    private function adjustCommitDateFormat(Commit $commit): array
    {
        $attributes = $commit->attributesToArray();

        if (isset($attributes['date'])) {
            if ($attributes['date'] instanceof DateTime) {
                $attributes['date'] = $attributes['date']->format('Y-m-d H:i:s');
            } elseif (is_string($attributes['date'])) {
                $date = new DateTime($attributes['date']);
                $attributes['date'] = $date->format('Y-m-d H:i:s');
            }
        }

        $now = date('Y-m-d H:i:s');
        $attributes['created_at'] = $now;
        $attributes['updated_at'] = $now;
        return $attributes;
    }
} 