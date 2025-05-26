<?php

namespace App\Api;

use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;

class GitHubApiClient implements ApiClientInterface
{
    private ClientInterface $client;
    private const BASE_URL = 'https://api.github.com';
    private const USER_AGENT = 'circunomics-git-api-service';
    private const PER_PAGE_LIMIT = 100; // GitHub API max page limit

    public function __construct(?ClientInterface $client = null)
    {
        $this->client = $client ?? new Client([
            'base_uri' => self::BASE_URL,
            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'User-Agent' => self::USER_AGENT,
            ],
        ]);
    }

    public function getPlatform(): string
    {
        return 'github';
    }

    /**
     * @param string $owner Repository owner
     * @param string $repo Repository name
     * @param int $limit Maximum number of commits to retrieve
     * @return array
     * @throws GitHubApiException
     */
    public function getMostRecentCommits(string $owner, string $repo, int $limit = 1000): array
    {
        try {
            return $this->fetchAllPages(
                $this->buildCommitsEndpoint($owner, $repo),
                $limit,
                [$this, 'transformCommitData']
            );
        } catch (Throwable $e) {
            throw new GitHubApiException(
                'API request failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    private function buildCommitsEndpoint(string $owner, string $repo): string
    {
        return "/repos/{$owner}/{$repo}/commits";
    }

    /**
     * @param string $endpoint
     * @param int $limit
     * @param callable $transformer
     * @return array
     * @throws GuzzleException
     * @throws GitHubApiException
     */
    private function fetchAllPages(string $endpoint, int $limit, callable $transformer): array
    {
        $page = 1;
        $items = [];

        while (count($items) < $limit && $page <= ceil($limit / self::PER_PAGE_LIMIT)) {
            $response = $this->client->get($endpoint, [
                'query' => [
                    'per_page' => self::PER_PAGE_LIMIT,
                    'page' => $page,
                ],
            ]);

            if ($response->getStatusCode() >= 400) {
                throw new GitHubApiException('API request failed: ' . $response->getReasonPhrase());
            }

            $data = $this->parseResponse($response);

            if (empty($data)) {
                break; // No more items
            }

            foreach ($data as $item) {
                $items[] = call_user_func($transformer, $item);

                if (count($items) >= $limit) {
                    break;
                }
            }

            $page++;
        }

        return $items;
    }

    /**
     * @param array $rawData
     * @return array
     * @throws Exception
     */
    private function transformCommitData(array $rawData): array
    {
        $authorName = $rawData['commit']['author']['name'] ?? 'Unknown';
        $authorDate = $rawData['commit']['author']['date'] ?? null;

        return [
            'hash' => $rawData['sha'],
            'author' => $authorName,
            'date' => $authorDate ? new DateTime($authorDate) : new DateTime(),
        ];
    }

    private function parseResponse($response): array
    {
        return json_decode($response->getBody()->getContents(), true) ?? [];
    }
}
