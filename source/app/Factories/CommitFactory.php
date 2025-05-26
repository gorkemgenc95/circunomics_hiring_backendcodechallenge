<?php

namespace App\Factories;

use App\Models\Commit;
use DateTime;
use Exception;
use InvalidArgumentException;

class CommitFactory
{
    /**
     * @throws Exception
     */
    public function createFromGitHubData(array $commitData, string $owner, string $repo): Commit
    {
        $this->validateGitHubData($commitData);
        
        $date = new DateTime($commitData['commit']['author']['date']);
        
        return new Commit([
            'hash' => $commitData['sha'],
            'author' => $commitData['commit']['author']['name'],
            'date' => $date->format('Y-m-d H:i:s'),
            'repository_owner' => $owner,
            'repository_name' => $repo,
            'message' => $commitData['commit']['message'] ?? '',
        ]);
    }

    private function validateGitHubData(array $commitData): void
    {
        $required = ['sha', 'commit'];
        
        foreach ($required as $field) {
            if (!isset($commitData[$field])) {
                throw new InvalidArgumentException("Missing required field: {$field}");
            }
        }
        
        if (!isset($commitData['commit']['author']['name']) || !isset($commitData['commit']['author']['date'])) {
            throw new InvalidArgumentException("Missing required author data");
        }
    }
} 