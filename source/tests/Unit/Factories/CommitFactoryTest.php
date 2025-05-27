<?php

namespace Tests\Unit\Factories;

use App\Factories\CommitFactory;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CommitFactoryTest extends TestCase
{
    private CommitFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new CommitFactory();
    }

    public function testValidationThrowsExceptionForMissingSha(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required field: sha');

        $gitHubData = [
            'commit' => [
                'author' => [
                    'name' => 'John Doe',
                    'date' => '2023-01-01T12:00:00Z',
                ],
            ],
        ];

        $this->factory->createFromGitHubData($gitHubData, 'test', 'repo');
    }

    public function testValidationThrowsExceptionForMissingCommit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required field: commit');

        $gitHubData = [
            'sha' => 'abc123',
        ];

        $this->factory->createFromGitHubData($gitHubData, 'test', 'repo');
    }

    public function testValidationThrowsExceptionForMissingAuthorName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required author data');

        // author without name
        $gitHubData = [
            'sha' => 'abc123',
            'commit' => [
                'author' => [
                    'date' => '2023-01-01T12:00:00Z',
                ],
            ],
        ];

        $this->factory->createFromGitHubData($gitHubData, 'test', 'repo');
    }

    public function testValidationThrowsExceptionForMissingAuthorDate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required author data');

        // author without date data
        $gitHubData = [
            'sha' => 'abc123',
            'commit' => [
                'author' => [
                    'name' => 'John Doe',
                ],
            ],
        ];

        $this->factory->createFromGitHubData($gitHubData, 'test', 'repo');
    }

    public function testCreateFromPlatformDataWithGitLabThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('GitLab has not been implemented');

        $gitLabData = ['some' => 'data'];
        $this->factory->createFromPlatformData($gitLabData, 'owner', 'repo', 'gitlab');
    }

    public function testCreateFromPlatformDataWithBitbucketThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Bitbucket has not been implemented');

        $bitbucketData = ['some' => 'data'];
        $this->factory->createFromPlatformData($bitbucketData, 'owner', 'repo', 'bitbucket');
    }

    public function testCreateFromPlatformDataWithUnsupportedPlatformThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported platform: unsupported');

        $data = ['some' => 'data'];
        $this->factory->createFromPlatformData($data, 'owner', 'repo', 'unsupported');
    }
}
