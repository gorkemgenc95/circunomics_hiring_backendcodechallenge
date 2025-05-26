<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\ServiceContainer;

$services = ServiceContainer::getInstance();
$githubSyncer = $services->getGitHubCommitSyncService();
$githubSyncer->syncCommits('nodejs', 'node');
