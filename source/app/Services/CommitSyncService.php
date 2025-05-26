<?php

namespace App\Services;

use App\Api\ApiClientInterface;
use App\Api\GitHubApiClient;
use App\Api\GitHubApiException;
use App\Factories\CommitFactory;
use App\Repositories\CommitRepositoryInterface;
use App\Repositories\MySqlCommitRepository;
use Exception;

class CommitSyncService
{
    private ApiClientInterface $apiClient;

    private CommitRepositoryInterface $repository;

    private CommitFactory $commitFactory;

    private BatchProcessor $batchProcessor;

    private SyncLogger $logger;

    public function __construct(
        ?ApiClientInterface $apiClient = null,
        ?CommitRepositoryInterface $repository = null,
        ?CommitFactory $commitFactory = null,
        ?BatchProcessor $batchProcessor = null,
        ?SyncLogger $logger = null
    ) {
        $this->apiClient = $apiClient ?? new GitHubApiClient();
        $this->repository = $repository ?? new MySqlCommitRepository();
        $this->commitFactory = $commitFactory ?? new CommitFactory();
        $this->batchProcessor = $batchProcessor ?? new BatchProcessor($this->repository);
        $this->logger = $logger ?? new SyncLogger();
    }

    public function syncCommits(string $owner, string $repo, int $limit = 1000): array
    {
        try {
            $platform = $this->apiClient->getPlatform();
            $this->logger->info("Fetching $limit commits from $platform for $owner/$repo...");

            $apiCommits = $this->fetchCommitsFromApi($owner, $repo, $limit);
            $stats = $this->processAndSaveCommits($apiCommits, $owner, $repo);

            $this->logger->logSyncResults($stats);

            return $stats;

        } catch (Exception $e) {
            $this->logger->error("Error syncing commits: " . $e->getMessage());

            return [
                'fetched' => 0,
                'saved' => 0,
                'duplicates' => 0,
            ];
        }
    }

    /**
     * @throws GitHubApiException
     */
    private function fetchCommitsFromApi(string $owner, string $repo, int $limit): array
    {
        $apiCommits = $this->apiClient->getMostRecentCommits($owner, $repo, $limit);

        if (empty($apiCommits)) {
            $this->logger->info("No commits found from API.");

            return [];
        }

        $this->logger->info("Fetched " . count($apiCommits) . " commits from API.");

        return $apiCommits;
    }

    /**
     * @throws Exception
     */
    private function processAndSaveCommits(array $apiCommits, string $owner, string $repo): array
    {
        if (empty($apiCommits)) {
            return ['fetched' => 0, 'saved' => 0, 'duplicates' => 0];
        }

        $this->logger->info("Checking for duplicates and saving to database...");

        $commits = $this->transformApiCommitsToModels($apiCommits, $owner, $repo);
        $newCommits = $this->filterOutDuplicates($commits);

        $saved = $this->batchProcessor->saveCommits($newCommits);

        return [
            'fetched' => count($apiCommits),
            'saved' => $saved,
            'duplicates' => count($commits) - $saved,
        ];
    }

    /**
     * @throws Exception
     */
    private function transformApiCommitsToModels(array $apiCommits, string $owner, string $repo): array
    {
        $platform = $this->apiClient->getPlatform();
        $commits = [];

        foreach ($apiCommits as $commitData) {
            $commits[] = [
                'hash' => $commitData['hash'],
                'commit' => $this->commitFactory->createFromPlatformData([
                    'sha' => $commitData['hash'],
                    'commit' => [
                        'author' => [
                            'name' => $commitData['author'],
                            'date' => $commitData['date']->format('c'),
                        ],
                        'message' => '',
                    ],
                ], $owner, $repo, $platform),
            ];
        }

        return $commits;
    }

    private function filterOutDuplicates(array $commits): array
    {
        $newCommits = [];

        foreach ($commits as $commitData) {
            if (! $this->repository->existsByHash($commitData['hash'])) {
                $newCommits[] = $commitData['commit'];
            }
        }

        return $newCommits;
    }
}
