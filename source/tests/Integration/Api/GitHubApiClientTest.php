<?php

namespace Tests\Integration\Api;

use App\Api\GitHubApiClient;
use DateTime;
use PHPUnit\Framework\TestCase;

class GitHubApiClientTest extends TestCase
{
    private GitHubApiClient $client;

    protected function setUp(): void
    {
        $this->client = new GitHubApiClient();
    }

    public function testGetPlatformName(): void
    {
        $this->assertEquals('github', $this->client->getPlatform());
    }

    public function testGetRecentCommits(): void
    {
        $commits = $this->client->getMostRecentCommits('nodejs', 'node', 10);

        $this->assertCount(10, $commits);

        foreach ($commits as $commit) {
            $this->assertArrayHasKey('hash', $commit);
            $this->assertArrayHasKey('author', $commit);
            $this->assertArrayHasKey('date', $commit);

            $this->assertIsString($commit['hash']);
            $this->assertIsString($commit['author']);
            $this->assertInstanceOf(DateTime::class, $commit['date']);
            $this->assertEquals(40, strlen($commit['hash']));
        }
    }
}
