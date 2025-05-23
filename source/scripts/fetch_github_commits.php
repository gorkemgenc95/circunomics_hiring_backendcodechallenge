<?php

require_once __DIR__ . '/../app/bootstrap.php';

use App\Api\GitHubApiClient;

// Parse command line options
$options = getopt('', ['owner::', 'repo::', 'limit::']);
$owner = $options['owner'] ?? 'nodejs';
$repo = $options['repo'] ?? 'node';
$limit = isset($options['limit']) ? (int) $options['limit'] : 10;

echo "Fetching {$limit} recent commits from {$owner}/{$repo}...\n";

try {
    $client = new GitHubApiClient();
    $commits = $client->getMostRecentCommits($owner, $repo, $limit);
    
    echo "Retrieved " . count($commits) . " commits:\n";
    foreach ($commits as $index => $commit) {
        echo ($index + 1) . ". " . $commit['hash'] . " by " . $commit['author'] . 
             " on " . $commit['date']->format('Y-m-d H:i:s') . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 