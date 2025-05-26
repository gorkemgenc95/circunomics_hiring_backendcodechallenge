<?php

namespace App\Services;

use App\Repositories\CommitRepositoryInterface;

class CommitQueryService
{
    private CommitRepositoryInterface $repository;

    public function __construct(CommitRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getPaginatedCommits(int $page = 1, int $perPage = 20, ?string $platform = 'github', ?string $owner = null, ?string $repo = null): array
    {
        $offset = ($page - 1) * $perPage;

        $totalCommits = $this->repository->count($platform, $owner, $repo);
        $totalPages = (int) ceil($totalCommits / $perPage);

        $commits = $this->repository->findPaginated($offset, $perPage, $platform, $owner, $repo);

        $formattedCommits = array_map(function ($commit) {
            return [
                'hash' => $commit->getAttribute('hash'),
                'author' => $commit->getAttribute('author'),
                'repository_owner' => $commit->getAttribute('repository_owner'),
                'repository_name' => $commit->getAttribute('repository_name'),
                'platform' => $commit->getAttribute('platform'),
                'date' => $commit->getAttribute('date'),
            ];
        }, $commits);

        return [
            'commits' => $formattedCommits,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_pages' => $totalPages,
                'total_commits' => $totalCommits,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages,
            ],
        ];
    }
}
