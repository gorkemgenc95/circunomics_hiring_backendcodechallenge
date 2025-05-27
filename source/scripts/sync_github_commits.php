<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\ServiceContainer;

$owner = 'nodejs';
$repo = 'node';

echo "Fetching most recent commits from {$owner}/{$repo}...\n";

try {
    $client = ServiceContainer::getInstance();
    $githubSyncer = $client->getGitHubCommitSyncService();
    $result = $githubSyncer->syncCommits($owner, $repo);
    
    echo "Sync completed successfully!\n";
    echo "Fetched: {$result['fetched']} commits\n";
    echo "Saved: {$result['saved']} new commits\n";
    echo "Duplicates skipped: {$result['duplicates']}\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 