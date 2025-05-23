<?php

namespace Tests\Unit\Api;

use App\Api\GitHubApiClient;
use App\Api\GitHubApiException;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GitHubApiClientTest extends TestCase
{
    private const SAMPLE_COMMIT_HASH_1 = '1234567890abcdef1234567890abcdef12345678';
    private const SAMPLE_COMMIT_HASH_2 = 'abcdef1234567890abcdef1234567890abcdef12';
    private const SAMPLE_AUTHOR_1 = 'Gorkem Genc';
    private const SAMPLE_AUTHOR_2 = 'Genc Gorkem';
    private const SAMPLE_DATE_1 = '2025-05-23T12:00:00Z';
    private const SAMPLE_DATE_2 = '2025-05-23T14:00:00Z';

    public function testGetMostRecentCommitsReturnsCorrectData(): void
    {
        $mockResponseData = [
            $this->createCommitData(self::SAMPLE_COMMIT_HASH_1, self::SAMPLE_AUTHOR_1, self::SAMPLE_DATE_1),
            $this->createCommitData(self::SAMPLE_COMMIT_HASH_2, self::SAMPLE_AUTHOR_2, self::SAMPLE_DATE_2),
        ];

        $client = $this->createMockClient($mockResponseData);
        $commits = $client->getMostRecentCommits('test-owner', 'test-repo', 2);

        $this->assertCommitsMatchExpected($commits, $mockResponseData);
    }

    public function testGetMostRecentCommitsHandlesPagination(): void
    {
        $page1Data = $this->createCommitArrayOfSize(100, self::SAMPLE_COMMIT_HASH_1, self::SAMPLE_AUTHOR_1);
        $page2Data = $this->createCommitArrayOfSize(50, self::SAMPLE_COMMIT_HASH_2, self::SAMPLE_AUTHOR_2);

        $client = $this->createMockClient([$page1Data, $page2Data]);
        $commits = $client->getMostRecentCommits('test-owner', 'test-repo', 150);

        $this->assertCount(150, $commits);
        $this->assertEquals(self::SAMPLE_AUTHOR_1, $commits[0]['author']);
        $this->assertEquals(self::SAMPLE_AUTHOR_2, $commits[100]['author']);
    }

    public function testApiErrorHandling(): void
    {
        $client = $this->createMockClient([], 404);

        $this->expectException(GitHubApiException::class);
        
        $client->getMostRecentCommits('test-owner', 'nonexistent-repo', 1);
    }

    public function testEmptyResponseHandling(): void
    {
        $client = $this->createMockClient([]);
        $commits = $client->getMostRecentCommits('test-owner', 'test-repo', 10);

        $this->assertEmpty($commits);
    }

    private function createCommitData(string $hash, string $author, string $date = self::SAMPLE_DATE_1): array
    {
        return [
            'sha' => $hash,
            'commit' => [
                'author' => [
                    'name' => $author,
                    'date' => $date,
                ],
            ],
        ];
    }

    private function createCommitArrayOfSize(int $size, string $hash, string $author): array
    {
        return array_fill(0, $size, $this->createCommitData($hash, $author));
    }

    private function createMockClient(array $responses, int $statusCode = 200): GitHubApiClient
    {
        if (empty($responses) || (isset($responses[0]) && isset($responses[0]['sha']))) {
            $responses = [$responses];
        }

        $mockResponses = array_map(function ($data) use ($statusCode) {
            return new Response($statusCode, ['Content-Type' => 'application/json'], json_encode($data));
        }, $responses);

        $mock = new MockHandler($mockResponses);
        $handlerStack = HandlerStack::create($mock);
        $mockClient = new Client(['handler' => $handlerStack]);

        return new GitHubApiClient($mockClient);
    }

    private function assertCommitsMatchExpected(array $actualCommits, array $expectedData): void
    {
        $this->assertCount(count($expectedData), $actualCommits);

        foreach ($expectedData as $index => $expected) {
            $actual = $actualCommits[$index];
            
            $this->assertEquals($expected['sha'], $actual['hash']);
            $this->assertEquals($expected['commit']['author']['name'], $actual['author']);
            $this->assertInstanceOf(DateTime::class, $actual['date']);
        }
    }
}
